## Buddha 框架

#### Ioc,Di,注解,框架已经实现;开发者模式支持代码热更新;

#### Start

- Http Server start

```bash
[root@buddha buddha]# php boot {start|stop|restart}
```

#### Route

- 使用注解路由

```PHP
/**
 * @RequestMapping(value="/user/{uid:\d+}",method={"GET"})
 */
public function user( Request $request, $uid, Response $response)
{
    
}
```
#### DB
- 使用注解的方式, 选择default数据库
```PHP
/**
 * @DB(source = "default")
 * @var MyDB
 */
public $db;
```
#### 模型
```PHP
User::find(1);
```
#### 事务
- DB事务
```PHP
$db = $this->db1->Begin();
$this->db1->table('test')->insert(['user_name'=>'zhangshan','age'=>1]);
$db->Commit();

- 模型事务
$db = $this->db1->Begin();
$users = new User();
$users->user_name = 1;
$users->age = 10;
$users->save();

$test = Test::find(1);
$test->test_age = 1;
$test->save();
$db->Commit();
```
#### Redis
- 给执行结果添加redis缓存
```PHP
/**
 * @Redis(key="name")
 */
public function user( Request $request, $uid, Response $response)
{
    $this->db->table('test')->get();
}
```

#### 更多内容正在研究中......
