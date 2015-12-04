<?php namespace Lanin\Laravel\SetupWizard\Tests\Commands\Steps;

use Illuminate\Database\Eloquent\Model;
use Lanin\Laravel\SetupWizard\Commands\Setup;
use Lanin\Laravel\SetupWizard\Commands\Steps\CreateUser;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class CreateUserTest extends TestCase
{
    /**
     * @return \Mockery\Mock
     */
    public function mockCommand()
    {
        $command = \Mockery::mock(Setup::class)->makePartial();
        $command->shouldReceive('confirm')->with('Everything is right?')->andReturn(true);

        return $command;
    }

    /** @test */
    public function it_has_prompt_text()
    {
        $step = new CreateUser($this->mockCommand());
        $this->assertEquals('Create new user?', $step->prompt());
    }

    /** @test */
    public function it_can_use_custom_table()
    {
        $command = $this->mockCommand();
        $step = new CreateUser($command);

        $getTable = $this->getPublicMethod('getTable', $step);
        $this->assertEquals('foo', $getTable->invoke($step, 'foo'));
    }

    /** @test */
    public function it_can_use_table_from_database_driver()
    {
        $config = $this->app['config'];
        $config->set('auth.driver', 'database');

        $command = $this->mockCommand();
        $step = new CreateUser($command);

        $getTable = $this->getPublicMethod('getTable', $step);
        $this->assertEquals(config('auth.table'), $getTable->invoke($step));
    }

    /** @test */
    public function it_can_use_table_from_eloquent_driver()
    {
        $config = $this->app['config'];
        $config->set('auth.model', TestUser::class);

        $command = $this->mockCommand();
        $step = new CreateUser($command);

        $getTable = $this->getPublicMethod('getTable', $step);
        $this->assertEquals('test_users', $getTable->invoke($step));
    }

    /** @test */
    public function it_asks_for_user_info()
    {
        $fields = config('setup.create_user.fields');

        $command = $this->mockCommand();
        foreach ($fields as $key => $title)
        {
            $command->shouldReceive('ask')->with($title)->andReturn($title)->once();
        }

        $step = \Mockery::mock(CreateUser::class, [$command])->makePartial()->shouldAllowMockingProtectedMethods();
        $step->shouldReceive('getTable')->andReturn('test_users');

        $return = $step->prepare();

        foreach ($fields as $key => $title)
        {
            $this->assertArrayHasKey($key, $return);
        }

        $this->assertArrayHasKey('__table', $return);
        $this->assertEquals('test_users', $return['__table']);
    }

    /** @test */
    public function it_shows_preview()
    {
        $command = $this->mockCommand();
        $command->shouldReceive('info')->with(
            'I will insert this values into <comment>test_users</comment> table'
        )->once();
        $command->shouldReceive('table')->with(
            ['column', 'value'],
            [
                ['name', 'John'],
                ['email', 'john@example.com'],
                ['password', 'hash'],
            ]
        )->once();

        $step = new CreateUser($command);
        $this->assertNull($step->preview([
            'name' => 'John', 'email' => 'john@example.com', 'password' => 'hash', '__table' => 'test_users'
        ]));
    }

    /** @test */
    public function it_can_save_user()
    {
        $query = \Mockery::mock('TestQueryBuilder');
        $query->shouldReceive('insert')->with([
            'name' => 'John', 'email' => 'john@example.com', 'password' => 'hash'
        ])->andReturn(true);

        $db = \Mockery::mock($this->app['db']);
        $db->shouldReceive('table')->with('test_users')->andReturn($query);

        $this->app['db'] = $db;

        $step = new CreateUser($this->mockCommand());
        $this->assertTrue($step->finish([
            'name' => 'John', 'email' => 'john@example.com', 'password' => 'hash', '__table' => 'test_users'
        ]));
    }
}

class TestUser extends Model
{
    protected $table = 'test_users';
}