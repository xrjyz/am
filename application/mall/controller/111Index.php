<?php
namespace app\mall\controller;

use think\Controller;

/**
 * 首页
 */
class Index extends Common{

    public function __construct()
    {
        parent::__construct();
        session('shop', null);
        session('arrid', null);
    }
    /**
     * 首页
     * @return [type] [description]
     */
    public function index()
    {
        if (is_post()) {
            $data = input('post.');
           
            $this->order = 'prod_sort desc,id desc';
            $this->size = 6;
            $this->map[] = ['prod_status','=',1]; 
            if(!$data['prod_name']){
               
                switch(true) {
                    case $data['type']==0:
                        $this->map[] = ['prod_zone','=',0]; 
                        break;
                    case $data['type']==5:
                        $this->map[] = ['prod_is_hot','=',1]; 
                        break;
                    case $data['type']==6:
                        $this->map[] = ['prod_is_new','=',1]; 
                        break;
                    default:
                        break;
                }
            }else{
                $this->map[] = ['prod_name','like',"%".$data['prod_name']."%"];
            }

            $list = model('StoProd')->chaxun($this->map,$this->order,$this->size);
           
            $html = '';
            foreach ($list as $k => $v) {
                $arr = explode(',',$v['prod_pic']);
                $pic = $arr[0];
               /* if ($v['pd_zone'] == 2) {
                    $htm = '<p class="price_credit"><img src="/static/mobile/img/xianjin.png" alt="" /><span>' . $v['pd_discount_price'] . '</span><span>+</span><img src="/static/mobile/img/aa.png" alt="" /><span>' . $v['pd_integrity_price'] . '</span></p>';
                } elseif ($v['pd_zone'] == 3) {
                    $htm = '<p class="credit"><span><img src="/static/mobile/img/aa.png" alt="" /><span>' . $v['pd_integrity_price'] . '</span></span><span><span>¥</span><span>' . $v['pd_yuan_price'] . '</span></span></p>';
                } else {
                    $htm = '<p class="price"><span><span>¥</span><span>' . $v['pd_discount_price'] . '</span></span><span><span>¥</span><span>' . $v['pd_yuan_price'] . '</span></span></p>';
                }*/
                $htm = '<p class="price"><span><span>¥</span><span>' . $v['prod_price'] . '</span></span><span><span>¥</span><span>' . $v['prod_price'] . '</span></span></p>';

                $url = "/mall/index/detail?id=" . $v['id'];
                // $html .= '<li class="price_li"><div class="sp"><img src="/uploads/pd/' . $pic . '"/></div><p class="hiddenp">' . $v['prod_name'] . '</p>' . $htm . '<p class="integral"><span>可得康宝币<span>' . $v['prod_price'] . '</span></span><span>已售<span>' . $v['prod_sales'] . '</span></span></p><a href="' . $url . '"></a></li>';
                 $html .= '<li class="price_li"><div class="sp"><img src="' . $pic . '"/></div><p class="hiddenp">' . $v['prod_name'] . '</p>' . $htm . '<p class="integral"><span>活动中...<span></span></span><span>已售<span>' . $v['prod_sales'] . '</span></span></p><a href="' . $url . '"></a></li>';
            }

            echo json_encode($html);
            return;
        }
        $news = model('Message')->where('me_type', 1)->order($this->order)->limit(3)->select();
        $where = array(
            'shuff_status' => 1,
            'shuff_type' => 0,
            'shuff_loca' => 10,
        );
        $shuff = model('Shuff')->where($where)->order('shuff_sort desc,id desc')->limit(3)->select();
        $this->assign(array(
            'shuff' => $shuff,
            'news' => $news,
        ));
        return $this->fetch();
    }
    /**
     * 商品首页
     */
    public function home()
    {

        if (is_post()) {
            $data = input('post.');
            $this->order = 'prod_sort desc,id desc';
            $this->size = 6;
            $this->map[] = ['prod_status','=',1]; 
            $this->map[] = ['prod_zone','=',$data['zone_type']]; 
            

            if($data['pcate']!=0){
                if($data['child_cate']){
                    $this->map[] = ['cate_id','=',$data['child_cate']]; 
                }else{
                    $cate = model('StoCate')->where('cate_pid',$data['pcate'])->select();
                    $arr = [];
                    foreach ($cate as $k => $v) {
                        array_push($arr, $v["id"]);
                    }
                    $this->map[] = ['cate_id','in',$arr]; 
                }
            }
            $list = model('StoProd')->chaxun($this->map,$this->order,$this->size);
            $html = '';
            foreach ($list as $k => $v) {
                $arr = explode(',',$v['prod_pic']);
                $pic = $arr[0];
                $url = "/mall/index/detail?id=" . $v['id'];

                if($data['zone_type']==1){
                    $htm = '<p class="credit"><span><img src="/static/mall/img/aa.png" alt="" /><span>' . $v['prod_price'] . '</span></span></p>';
                    $html .= '<li class="price_li"><div class="sp"><img src="' . $pic . '"/></div><p class="hiddenp">' . $v['prod_name'] . '</p>' . $htm . '<p class="integral"><span>已售<span>' . $v['prod_sales'] . '</span></span></p><a href="' . $url . '"></a></li>';
                }elseif($data['zone_type']==0){
                    $htm = '<p class="price"><span><span>¥</span><span>' . $v['prod_price'] . '</span></span><span><span>¥</span><span>' . $v['prod_price_yuan'] . '</span></span></p>';
                    $html .= '<li class="price_li"><div class="sp"><img src="' . $pic . '"/></div><p class="hiddenp">' . $v['prod_name'] . '</p>' . $htm . '<p class="integral"><span>已售<span>' . $v['prod_sales'] . '</span></span></p><a href="' . $url . '"></a></li>';
                }elseif($data['zone_type']==3){
                    $htm = '<p class="price"><span><span>团购价' . $v['prod_price'] . '</span><span></span></span><span><span>¥</span><span>' . $v['prod_price_yuan'] . '</span></span></p>';
                    $html .= '<li class="price_li"><div class="sp"><img src="' . $pic . '"/></div><p class="hiddenp">' . $v['prod_name'] . '</p>' . $htm . '<p class="integral"><span>参团人数&nbsp;&nbsp;<span>' .$v['prod_sales']  . '</span></span></p><a href="' . $url . '"></a></li>';
                }
                
                
            }
            echo json_encode($html);return;
           
            // foreach ($list as $k => $v) {
            //     if ($v['pd_zone'] == 2) {
            //         $htm = '<p class="price_credit"><img src="/static/mobile/img/xianjin.png" alt="" /><span>' . $v['pd_discount_price'] . '</span><span>+</span><img src="/static/mobile/img/aa.png" alt="" /><span>' . $v['pd_integrity_price'] . '</span></p>';
            //     } elseif ($v['pd_zone'] == 3) {
            //         $htm = '<p class="credit"><span><img src="/static/mobile/img/aa.png" alt="" /><span>' . $v['pd_integrity_price'] . '</span></span><span><span>¥</span><span>' . $v['pd_yuan_price'] . '</span></span></p>';
            //     } else {
            //         $htm = '<p class="price"><span><span>¥</span><span>' . $v['pd_discount_price'] . '</span></span><span><span>¥</span><span>' . $v['pd_yuan_price'] . '</span></span></p>';
            //     }
            //     $url = "/mall/index/detail?id=" . $v['id'];
            //     $html .= '<li class="price_li"><div class="sp"><img src="/uploads/pd/' . $v['pd_pic'] . '"/></div><p class="hiddenp">' . $v['pd_name'] . '</p>' . $htm . '<p class="integral"><span>已售<span>' . $v['order_xian'] . '</span></span></p><a href="' . $url . '"></a></li>';
            // }
        }
        // dump(session('zone_type'));
        // dump(session('cate_id'));
        $zone_type = input('get.type');
        if($zone_type ==''){
            $zone_type = session('zone_type');
        }else{
            session('zone_type', $zone_type);
        }

        $cate_id = input('get.cate');
        if( $cate_id==''){
            if(session('cate_id')==""){
                $cate_id = 0;
                session('cate_id',0); 
            }else{
               $cate_id = session('cate_id'); 
            }
        }else{
           session('cate_id', $cate_id);
        }


        $map1 = [
            'cate_pid' => 0,
            'cate_status' => 1,
        ];
        $cate = model('StoCate')->where($map1)->order('cate_sort desc')->select(); //一级分类


        if($cate_id){
            $cate2 = model("StoCate")->where('cate_pid',$cate_id)->select();
            $this->assign('cate2',$cate2);
        }
        
        $zone = prod_zone($zone_type);
        

        $map2 = array(
            'shuff_status' => 1,
            'shuff_type' => 0,
            'shuff_loca' => $zone_type,
            
        );
        $order2 = 'shuff_sort desc,id desc';
        $shuff = model('Shuff')->where($map2)->order($order2)->limit(3)->select();
        
        if (!count($shuff)) {
            $map2['shuff_loca'] = 10;
            $shuff = model('Shuff')->where($map2)->order($order2)->limit(3)->select();
        }

        // $where = array(
        //     'shuff_status' => 1,
        //     'shuff_type' => 0,
        //     'shuff_loca' => 10,
        // );
        // $shuff = model('Shuff')->where($where)->order('shuff_sort desc,id desc')->limit(3)->select();

        $this->assign(array(
            'shuff' => $shuff, //轮播图
            'cate' => $cate,   //一级分类
            'zone_type' => $zone_type,  //分区
            'zone' => $zone,    //分区名称
        ));
        return $this->fetch();
    }


    /**
     * 全部商品一级分类列表页
     */
    public function shop()
    {
        //产品类型
        if (session('type')) {
            $get_type = session('type');
        } else {
            $get_type = 0;
        }

        if (is_post()) {
            $p = input('p') ? input('p') : 1;
            $map = array(
                'status' => 2,
            );
            $page = $p . ',6';
            if ($get_type == 9) {
                $map['pd_zone'] = array('in', '4,5,6,7,8');
            } elseif ($get_type == 11) {
                $map['pd_zone'] = array('in', '2,3');
            } elseif ($get_type) {
                $map['pd_zone'] = $get_type;
            }
            $cate_zi = db('cate')->where('pid', input('pcate'))->select();
            $arr = [(int) input('get.pcate')];
            foreach ($cate_zi as $k => $v) {
                array_push($arr, $v["id"]);
            }
            $where['cate_id'] = array('in', $arr);
            $list = model('Product')->where($map)->page($page)->order('sort desc,id desc')->select();
            $html = '';
            foreach ($list as $k => $v) {
                if ($v['pd_zone'] == 2) {
                    $htm = '<p class="price_credit"><img src="/static/mobile/img/xianjin.png" alt="" /><span>' . $v['pd_discount_price'] . '</span><span>+</span><img src="/static/mobile/img/aa.png" alt="" /><span>' . $v['pd_integrity_price'] . '</span></p>';
                } elseif ($v['pd_zone'] == 3) {
                    $htm = '<p class="credit"><span><img src="/static/mobile/img/aa.png" alt="" /><span>' . $v['pd_integrity_price'] . '</span></span><span><span>¥</span><span>' . $v['pd_yuan_price'] . '</span></span></p>';
                } else {
                    $htm = '<p class="price"><span><span>¥</span><span>' . $v['pd_discount_price'] . '</span></span><span><span>¥</span><span>' . $v['pd_yuan_price'] . '</span></span></p>';
                }
                $url = "/mall/index/detail?id=" . $v['id'];
                $html .= '<li class="price_li" data-cate="{$v.cate_id}" data-zone="{$v.pd_zone}"><div class="sp"><img src="/uploads/pd/' . $v['pd_pic'] . '"/></div><p class="hiddenp">' . $v['pd_name'] . '</p>' . $htm . '<p class="integral"><span>已售<span>' . $v['order_xian'] . '</span></span></p><a href="' . $url . '"></a></li>';
            }
            echo json_encode($html);return;
        }
        $type = type($get_type);
        //产品分类
        $cate_zi = db('cate')->where('pid', input('pcate'))->select();
        //分类导航
        $map = [
            'pid' => 0,
            'status' => 1,
        ];
        $cate = model('Cate')->where($map)->order('sort desc')->select();
        //轮播图
        $wherf = array(
            'on' => 1,
            'type' => 1,
            'place' => session('type'),
        );
        $shuffling = db('titlepic')->where($wherf)->order('sort desc,id desc')->limit(3)->select();
        if (!$shuffling) {
            $wherg = array(
                'on' => 1,
                'type' => 1,
                'place' => 0,
            );
            $shuffling = db('titlepic')->where($wherg)->order('sort desc,id desc')->limit(3)->select();
        }
        $this->assign(array(
            'shuffling' => $shuffling,
            'catelist' => $cate,
            'cate_zi' => $cate_zi,
            'type' => $type,
        ));
        return $this->fetch();
    }
    public function detail()
    {
        session('shop', null);
        session('arrid', null);
        $id = input('get.id');
        $info = model('StoProd')->detail(['id'=>$id]);
        $info['prod_pic'] = explode(',',$info['prod_pic']);
        $info['prod_describe'] = html_entity_decode($info['prod_describe']);
        // halt($info);
        $this->map[] = ['prod_id','=',$id];
        $this->order = 'attr_pid';
        $attr = model('StoProdAttr')->chaxun($this->map, $this->order, 80);
        // halt($attr);
        // 属性
        $list = [];
        $i = 0;
        foreach ($attr as $k => $v) {
            if (!isset($list[$v['attr_pid']])) {
                $i++;
                $list[$v['attr_pid']]['key'] = $v['attrp']['attr_name'];
                $list[$v['attr_pid']]['son'][] = $v['attr']['attr_name'];
                $list[$v['attr_pid']]['i'] = $i;
            } else {
                // $list[$value['patt_id']]['key'] = $value['patt_id_text'];
                $list[$v['attr_pid']]['son'][] = $v['attr']['attr_name'];
                // $list[$value['patt_id']]['i'] = $i;
                // $i++;
            }
        }
        $count = count($list);
        // halt($count);
        $collect = 0;
        if(session('us_id')){
            //是否收藏
            $arr = explode(',', $this->mine['us_collect']);
            if (in_array($id, $arr)) {
                $collect = 1;
            }
        }
        
        $this->assign(array(
            'info' => $info,
            'list' => $list,
            'count' => $count,
            'collect' => $collect,
        ));

        return $this->fetch();
    }


   
   
}
