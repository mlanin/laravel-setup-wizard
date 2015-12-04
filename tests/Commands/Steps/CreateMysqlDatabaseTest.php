<?php namespace Lanin\Laravel\SetupWizard\Tests\Commands\Steps;

use Illuminate\Database\MySqlConnection;
use Lanin\Laravel\SetupWizard\Commands\Setup;
use Lanin\Laravel\SetupWizard\Commands\Steps\CreateMysqlDatabase;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class CreateMysqlDatabaseTest extends TestCase
{
    private $databaseConfig = [
        'host'      => '10.0.0.0',
        'database'  => 'setup_test',
        'username'  => 'setup_user',
        'password'  => 'qwe123',
    ];

    private $databaseHost = 'setup_host';

    public function setUp()
    {
        parent::setUp();

        $this->mockDatabase();
    }

    private function mockDatabase()
    {
        $mysql = \Mockery::mock(MysqlConnection::class)->makePartial();

        foreach ($this->databaseConfig as $key => $value)
        {
            $mysql->shouldReceive('getConfig')->with($key)->andReturn($value);
        }

        \DB::shouldReceive('connection')->andReturn($mysql);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

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
        $step = new CreateMysqlDatabase($this->mockCommand());
        $this->assertEquals('Do you want to create MySQL database?', $step->prompt());
    }

    /** @test */
    public function it_can_get_database_configs()
    {
        $command = $this->mockCommand();
        $command->shouldReceive('ask')->with('Database is on the other server. Provide local IP/hostname.')->andReturn($this->databaseHost)->once();

        $step = new CreateMysqlDatabase($command);
        $getDatabaseConfigs = $this->getPublicMethod('getDatabaseConfigs', $step);

        $return = [];

        $getDatabaseConfigs->invokeArgs($step, [&$return]);

        foreach ($this->databaseConfig as $item => $value)
        {
            $this->assertEquals($value, $return[$item]);
        }

        $this->assertEquals($this->databaseConfig['username'], $return['connection_username']);
        $this->assertEquals($this->databaseConfig['password'], $return['connection_password']);
        $this->assertEquals($this->databaseHost, $return['localhost']);
    }

    /** @test */
    public function it_can_ask_for_an_other_user()
    {
        $username = 'root';
        $password = 'password';

        $command = $this->mockCommand();
        $command->shouldReceive('ask')->with(
            'Provide user\'s login with <comment>CREATE DATABASE</comment> grants',
            'root'
        )->andReturn($username)->once();
        $command->shouldReceive('secret')->with('Password')->andReturn($password)->once();

        $step = new CreateMysqlDatabase($command);
        $getOtherConnectionUser = $this->getPublicMethod('getOtherConnectionUser', $step);

        $return = [];

        $getOtherConnectionUser->invokeArgs($step, [&$return]);

        $this->assertEquals($username, $return['connection_username']);
        $this->assertEquals($password, $return['connection_password']);
    }

    /** @test */
    public function it_can_generate_sql_commands()
    {
        $step = new CreateMysqlDatabase($this->mockCommand());
        $generateSqlCommands = $this->getPublicMethod('generateSqlCommands', $step);

        $return = $this->databaseConfig;
        $return['localhost'] = $this->databaseHost;

        $generateSqlCommands->invokeArgs($step, [&$return]);

        $this->assertEquals("CREATE DATABASE IF NOT EXISTS {$this->databaseConfig['database']};", $return['commands'][0]);
        $this->assertEquals(sprintf(
            "GRANT ALL PRIVILEGES ON %s.* TO %s@%s IDENTIFIED BY '%s';",
            $this->databaseConfig['database'],
            $this->databaseConfig['username'],
            $this->databaseHost,
            $this->databaseConfig['password']
        ), $return['commands'][1]);
    }

    /** @test */
    public function it_can_generate_console_command()
    {
        $username = 'root';
        $password = 'password';

        $step = new CreateMysqlDatabase($this->mockCommand());
        $generateConsoleCommand = $this->getPublicMethod('generateConsoleCommand', $step);

        $return = $this->databaseConfig;
        $return['localhost'] = $this->databaseHost;
        $return['connection_username'] = $username;
        $return['connection_password'] = $password;
        $return['commands'] = ['Foo', 'Bar'];

        $command = $generateConsoleCommand->invokeArgs($step, [$return, true]);
        $this->assertEquals("mysql -u\"{$username}\" -p\"{$password}\" -h\"{$this->databaseConfig['host']}\" -e\"Foo Bar\"", $command);

        $command = $generateConsoleCommand->invokeArgs($step, [$return, false]);
        $this->assertEquals("mysql -u\"{$username}\" -p\"******\" -h\"{$this->databaseConfig['host']}\" -e\"Foo Bar\"", $command);
    }

    /** @test */
    public function it_can_use_config_user_to_create_db()
    {
        $command = $this->mockCommand();
        $command->shouldReceive('confirm')->with('Do you want to provide an other user to connect to DB?', false)->andReturn(false)->once();
        $command->shouldReceive('ask')->with('Database is on the other server. Provide local IP/hostname.')->andReturn($this->databaseHost)->once();

        $step = new CreateMysqlDatabase($command);

        $return = $step->prepare();

        foreach ($this->databaseConfig as $item => $value)
        {
            $this->assertEquals($value, $return[$item]);
        }

        $this->assertEquals($this->databaseConfig['username'], $return['connection_username']);
        $this->assertEquals($this->databaseConfig['password'], $return['connection_password']);
        $this->assertEquals($this->databaseHost, $return['localhost']);

        $commands = $return['commands'];
        $this->assertArrayHasKey(0, $commands);
        $this->assertArrayHasKey(1, $commands);
    }


    /** @test */
    public function it_can_run_prepare_and_gather_all_info_and_commands()
    {
        $command = $this->mockCommand();
        $command->shouldReceive('confirm')->with('Do you want to provide an other user to connect to DB?', false)->andReturn(true)->once();

        $step = \Mockery::mock(CreateMysqlDatabase::class, [$command])->makePartial()->shouldAllowMockingProtectedMethods();
        $step->shouldReceive('getDatabaseConfigs')->once();
        $step->shouldReceive('getOtherConnectionUser')->once();
        \Mockery::getConfiguration()->setInternalClassMethodParamMap(CreateMysqlDatabase::class, 'generateSqlCommands', ['&$return']);
        $step->shouldReceive('generateSqlCommands')->with(\Mockery::on(function(&$return)
        {
            if (!is_array($return)) return false;
            $return = $this->databaseConfig;
            $return['localhost'] = $this->databaseHost;
            $return['connection_username'] = $this->databaseConfig['username'];
            $return['connection_password'] = $this->databaseConfig['password'];
            $return['commands'] = ['Foo', 'Bar'];
            return true;
        }))->once();

        $return = $step->prepare();

        foreach ($this->databaseConfig as $item => $value)
        {
            $this->assertEquals($value, $return[$item]);
        }

        $this->assertEquals($this->databaseConfig['username'], $return['connection_username']);
        $this->assertEquals($this->databaseConfig['password'], $return['connection_password']);
        $this->assertEquals($this->databaseHost, $return['localhost']);

        $this->assertArrayHasKey('commands', $return);

        $commands = $return['commands'];
        $this->assertArrayHasKey(0, $commands);
        $this->assertArrayHasKey(1, $commands);
    }

    /** @test */
    public function it_can_show_preview()
    {
        $command = $this->mockCommand();
        $command->shouldReceive('info')->with("This command will be executed: <comment>Foo Bar</comment>")->once();

        $step = \Mockery::mock(CreateMysqlDatabase::class, [$command])->makePartial()->shouldAllowMockingProtectedMethods();
        $step->shouldReceive('generateConsoleCommand')->andReturn('Foo Bar')->once();

        $step->preview([]);
    }

    /** @test */
    public function it_can_run_console_command()
    {
        $command = $this->mockCommand();
        $command->shouldReceive('line')->with("foo\n")->once();

        $step = \Mockery::mock(CreateMysqlDatabase::class, [$command])->makePartial()->shouldAllowMockingProtectedMethods();
        $step->shouldReceive('generateConsoleCommand')->andReturn('echo "foo"')->once();

        $this->assertTrue($step->finish([]));
    }
}