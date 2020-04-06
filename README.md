## Buddha 框架

#### Start

- Http Server

```bash
[root@buddha buddha]# php boot start
[root@buddha buddha]# php boot restart
[root@buddha buddha]# php boot stop
```
#### Ioc容器,Di,注解,框架已经实现,有兴趣可以一起探讨

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
- 把DB对象注解到了$db属性
```PHP
$users = $this->db->table('test')->where('votes', '>', 100)->get();
```
#### 模型
```PHP
User::find(1);
```

#### 连接池,mysql事务正在研究中......
