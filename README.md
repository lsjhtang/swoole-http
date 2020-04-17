## Buddha 框架

#### Ioc,Di,注解,连接池,框架已经实现;开发者模式支持代码热更新;

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
```
- 模型事务
```PHP
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
- redis注解 支持string hash set等类型
```PHP
/**
 * @Redis(key="name")
 */
public function user( Request $request, $uid, Response $response)
{
   return $this->db->table('test')->get();
}
```
- redis锁 lua脚本
```PHP
/**
 * @Lock(prefix="lock",key="#0")
 * @RequestMapping(value="/lock/{uid:\d+}")
 */
public function lock( Request $request, $uid, Response $response)
{
   return $this->db->table('test')->get();
}
```
#### 更多内容正在研究中......
