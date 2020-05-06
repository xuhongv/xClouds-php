<?php
/**
 * 项目名： AligenieTeach
 * 包名：RelDeviceUser
 * 创建时间：2019/6/29  9:50
 * 创建者博客： http://blog.csdn.net/xh870189248
 * 创建者GitHub： https://github.com/xuhongv
 * 创建者：徐宏 XuHongYss
 * 描述： TODO
 */

namespace app\common\model;


use think\db\Expression;
use think\Model;

class RelDeviceUser extends Model
{

    /**
     *
     * 根据条件统计
     * @param $where
     * @return int|string 条数
     */
    public function countWhere($where)
    {
        try {
            return $this->where($where)->count();
        } catch (\Exception $exception) {
            return 0;
        }
    }


    /**
     *
     *  查询指定条件的数据
     *
     * @param array $where 查询条件
     * @param string $option 指定返回的字段，默认是 device_id,remark
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getThisUserDevicesList($where = [], $option = 'device_id,uuid,online,alias,type,img')
    {

        //根据status排序
        $exp = new Expression('field(online,1,0)');
        try {
            return ($this->field($option)->where($where)->order($exp)->select());
        } catch (\Exception $exception) {
            return [];
        }
    }


    /**
     *
     *  查询指定条件的数据
     *
     * @param array $where 查询条件
     * @return array|false|\PDOStatement|string|\think\Collection  返回全部
     */
    public function getThisUserDevicesListDetial($where = [])
    {
        try {
            return ($this->where($where)->select());
        } catch (\Exception $exception) {
            return [];
        }
    }



}