<?php
namespace demo;


use core\handler\factory;


class test
{
    public $db;
    public static $tz = [
        'index' => [],
    ];
    public function init()
    {
        $this->db = factory::use('ext\pdo_mysql')
            ->pwd('root')
            ->db('yybserver');
    }
    public function index()
    {
//        $res = $this->db->select('question')->where(['like','shop_name','长沙%'])->where(['not','id','2,3,4,5'])->fetchAll(['id','shop_name']);
//        echo '<pre>';print_r($res);
//        exit();
        /**select**/
//        $res = $this->db->select('question')->where(['parent_type_id',2])->where(['question_type','3'])->order(['created_at','desc'])->limit(2,5)->fetchAll(['id','created_at']);
//        echo '<pre>';var_dump($res);
//        exit();
        /**insert**/
//        $data = [
//            [
//                'name' => '邹豆豆',
//                'sex' => '男',
//                'class' => '语文',
//                'score' => '120',
//                'age' => '16',
//            ],
//            [
//                'name' => '邹豆豆',
//                'sex' => '女',
//                'class' => '数学',
//                'score' => '121',
//                'age' => '17',
//            ],
//            [
//                'name' => '邹豆豆',
//                'sex' => '男',
//                'class' => '英语',
//                'score' => '122',
//                'age' => '18',
//            ],
//        ];
//        $res = $this->db->insert('pdo_test',$data)->exec();
//        echo '<pre>';var_dump($res);
//        exit();
        /**update**/
//        $data = [
//            'sex' => '女',
//        ];
//        $res = $this->db->update('pdo_test',$data)->order(['id','desc'])->increment('score',5)->decrement('age')->limit(3)->exec();
//        echo '<pre>';var_dump($res);exit();
//        exit();
        /**delete**/
//        $res = $this->db->delete('pdo_test')->order(['id','desc'])->limit(2)->exec();
//        echo '<pre>';print_r($res);exit();
//        exit();
        /**select having**/
//        $res = $this->db->select('pdo_test')->group(['name'])->having(['sum(score)','>','200'])->having(['avg(score)','>','72'])->order(['avg(score)','desc'])->limit(2)->fetchAll(['name','sum(score)','avg(score)']);
//        echo '<pre>';print_r($res);
//        exit();
        /**where between**/
//        $res = $this->db->table('pdo_test')->where(['class','数学'])->between(['score',80,120])->select(['name','class','score']);
//        echo '<pre>';print_r($res);
//        exit();
        /**join **/
//        $res = $this->db->select('sh_area_code as a')->join('sh_merchant_basic_information as b',[['a.id','=','b.area'],['a.code','=','b.city']],'right')->join('sh_pos_information as c',['b.id','=','c.merchant_id'])->fetchAll();
//        echo '<pre>';print_r($res);
//        exit();
    }
}