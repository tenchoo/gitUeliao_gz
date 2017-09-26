 <link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/foundation-datepicker.min.css">
 <link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/bootstrap.min.css">
     <link rel="stylesheet" href="http:////at.alicdn.com/t/font_80el9xaq2lxlayvi.css">
     <link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/sorting.css" />

    <nav class="container padding">
        <div class="pull-left rightimg">
            <img src="/themes/classic/statics/common/img/logo.png" alt="优易料" class="img-responsive logo">
        </div>
        <div class="col-lg-7 padding">
            <div class=" sec ">
                <select class="seclect">
                    <option>全部</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                </select>
                 <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="订单编号" class="text" />
              <!--   <input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="产品编号" class="text"> -->

                <input type="button" value="查找" class="but">
            </div>
        </div>
        <div class="pull-right leftdata">
            <div class="date" id="date">
                <span class="prefix"><img src="/themes/classic/statics/common/img/data.png" alt="" class="img">
                <input class="input-data" type="text"  onchange="change()"></span>
                <span class="span-data"><span class="span-data-data">这里是&nbsp;占&nbsp;位</span></span>
            </div>
        </div>
    </nav>
    <div class="container padding tips">
        <div class="col-lg-2">分拣次数:<span class="orange">11</span><span class="real">次</span></div>
        <div class="col-lg-2">分拣数量:<span class="orange">408</span><span class="real">米</span></div>
        <div class="col-lg-2">零码:<span class="orange">84</span><span class="real">米</span></div>
        <div class="col-lg-6">
            <div class="col-lg-2 padding">完成进度:</div>
            <div class="progress col-lg-6 padding">
                <div class="progress-bar">
                </div>
            </div>
            <span class="col-lg-2">35%</span>
        </div>
    </div>
    <section class="container padding">
        <div class="section-header">
            <table class="table mytable">
                <thead>
                    <tr>
                        <th style="color:red;"><span class="th-fir">产品编号</span></th>
                        <th style="color:red;">订单编号</th>
                        <th style="color:red;">下单时间</th>
                        <th style="color:red;">配送方式</th>
                        <th style="color:red;">分配数量</th>
                        <th style="color:red;">辅助单位</th>
                        <th style="color:red;">整卷</th>
                        <th style="color:red;">零码</th>
                        <th style="color:red;"><span class="th-las">操作</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach(  $list as $val  ){ ?>
                    <tr>
                      <td><?php echo $val['singleNumber'];?></td>
                      <td><?php echo $val['orderId'];?></td>
                      <td><?php echo $val['time'];?></td>
                      <td><?php echo $val['method'];?></td>
                      <td><?php echo $val['num'];?></td>
                      <td><?php echo $val['unit']; echo $val['unitname'];?></td>
                      <td><?php echo $val['int']; echo $val['unitname'];?></td>
                      <td><?php echo $val['remainder'];?></td>
                       <td>
                       <a a href=""  data-toggle="modal" class="bg-red red-border" onclick="sort('<?php echo $val['orderProductId'];?>',
                        '<?php echo $val['singleNumber'];?>',
                        '<?php echo $val['orderId'];?>',
                        '<?php echo $val['positionId'];?>',
                        '<?php echo $val['unit'];?>',
                        '<?php echo $val['num'];?>',
                         '<?php echo $val['name'];?>',
                         '<?php echo $user;?>',
                          '<?php echo  $val['memo'];?>',
                          '<?php echo  $val['warehouse'];?>',
                         '<?php echo  $val['singleNumber'];?>'
              )">分拣</a></td>

                    </tr>
                    <?php }?>
                    <tr>
                        <td>&nbsp;K538-411 浅灰</td>
                        <td>1610250035</td>
                        <td>2016/10/26 9:23</td>
                        <td>自提</td>
                        <td>67.0米</td>
                        <td>18米/卷</td>
                        <td>3卷</td>
                        <td>13.0米</td>
                        <td><a href="" class="bg-blue blue-border" data-toggle="modal" data-target="#myModal">归单</a></td>
                    </tr>
                    <tr>
                        <td class="black  ">&nbsp;K538-411 浅灰</td>
                        <td class="black  ">1610250035</td>
                        <td class="black  ">2016/10/26 9:23</td>
                        <td class="black  ">自提</td>
                        <td class="black  ">67.0米</td>
                        <td class="black  ">18米/卷</td>
                        <td class="black ">3卷</td>
                        <td class="black  ">13.0米</td>
                        <td class="black  "><a href="###" class="bg-gray gray-border" data-toggle="modal" data-target="#myModa2">完成</a></td>
                    </tr>
                    <tr class="gray">
                        <td>&nbsp;K538-411 浅灰</td>
                        <td>1610250035</td>
                        <td>2016/10/26 9:23</td>
                        <td>自提</td>
                        <td>67.0米</td>
                        <td>18米/卷</td>
                        <td>3卷</td>
                        <td> 13.0米</td>
                        <td><a href="###" class="bg-red red-border" data-toggle="modal" data-target="#myModa4">分拣</a></td>
                    </tr>
                    <tr class="yellow">
                        <td>&nbsp;K538-411 浅灰</td>
                        <td>1610250035</td>
                        <td>2016/10/26 9:23</td>
                        <td>自提</td>
                        <td>67.0米</td>
                        <td>18米/卷</td>
                        <td>3卷</td>
                        <td>13.0米</td>
                        <td><a href="###" class="bg-blue blue-border" data-toggle="modal" data-target="#myModal">归单</a></td>
                    </tr>
                    <tr class="gray">
                        <td>&nbsp;K538-411 浅灰</td>
                        <td>1610250035</td>
                        <td>2016/10/26 9:23</td>
                        <td>自提</td>
                        <td>67.0米</td>
                        <td>18米/卷</td>
                        <td>3卷</td>
                        <td>13.0米</td>
                        <td><a href="###" class="bg-gray gray-border" data-toggle="modal" data-target="#myModa2">完成</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                    待归单(k365-407)
                </h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr class="gray">
                            <td class="col-lg-4"><span class="font-gray">分&nbsp;拣&nbsp;员：</span>&nbsp;张三</td>
                            <td class="col-lg-8"><span class="font-gray">发货仓库：</span>中区门店仓</td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">客户名称：</span>京东</td>
                            <td><span class="font-gray">订&nbsp;&nbsp;单&nbsp;号：</span>&nbsp;1610250035</td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">订单数量：</span>67米</td>
                            <td><span class="font-gray">辅助数量：</span>18米/卷</td>
                        </tr>
                        <tr class="gray">
                            <td colspan="2"><span class="font-gray">特殊要求：</span><span class="red">标签贴在圈头,发韩国的料,包两层塑料</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-gray">整料仓位：</span>A1001</td>
                            <td><span class="font-gray">整料数量：</span>5卷</td>
                        </tr>
                        <tr>
                            <td><span class="font-gray">零码仓位：</span>A1023</td>
                            <td><span class="font-gray">零码数量：</span>10米+3米+2米</td>
                        </tr>
                        <tr class="yellow">
                            <td><span class="font-gray">标签数量：</span>4张</td>
                            <td><span class="font-gray">分拣数量：</span><span class="gui-orange">65</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;米<span class="red">少分拣2米</span></td>&nbsp;&nbsp;
                        </tr>
                        <tr class="yellow">
                            <td colspan="2"><span class="font-gray">分拣时间：</span>2016年11月3日 10:50</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default center-block" data-dismiss="modal">打印标签</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="myModa2" tabindex="-1" role="dialog" aria-labelledby="myModalLabe2" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabe2">
                    分拣完成(k365-407)
                </h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr class="gray">
                            <td class="col-lg-4"><span class="font-gray">分&nbsp;拣&nbsp;员：</span>&nbsp;张三</td>
                            <td class="col-lg-8"><span class="font-gray">发货仓库：</span>中区门店仓</td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">客户名称：</span>京东</td>
                            <td><span class="font-gray">订&nbsp;&nbsp;单&nbsp;号：</span>&nbsp;1610250035</td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">订单数量：</span>67米</td>
                            <td><span class="font-gray">辅助数量：</span>18米/卷</td>
                        </tr>
                        <tr class="gray">
                            <td colspan="2"><span class="gray">特殊要求：</span><span class="red">标签贴在圈头,发韩国的料,包两层塑料</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-gray">整料仓位：</span>A1001</td>
                            <td><span class="font-gray">整料数量：</span>5卷</td>
                        </tr>
                        <tr>
                            <td><span class="font-gray">零码仓位：</span>A1023</td>
                            <td><span class="font-gray">零码数量：</span>10米+3米+2米</td>
                        </tr>
                        <tr class="yellow">
                            <td><span class="font-gray">标签数量：</span>4张</td>
                            <td><span class="font-gray">分拣数量：</span><span class="wan-orange">65</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;米 <span class="red">少分拣2米</span></td>
                        </tr>
                        <tr class="yellow">
                            <td colspan="2"><span class="font-gray">分拣时间：</span>2016年11月3日 10:50</td>
                        </tr>
                        <tr>
                            <td colspan="2"><span class="font-gray">归单员：</span>李四于2016年11月3日 10:50将货归单完成</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default center-block" data-dismiss="modal">打印标签</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="myModa4" tabindex="-1" role="dialog" aria-labelledby="myModalLabe3" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabe3">
                    分拣&nbsp;(k365-407)
                </h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr class="gray">
                            <td class="col-lg-4"><span class="font-gray">分&nbsp;拣&nbsp;员：</span>&nbsp;张三</td>
                            <td class="col-lg-8"><span class="font-gray">发货仓库：</span>中区门店仓</td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">客户名称：</span>京东</td>
                            <td><span class="font-gray">订&nbsp;&nbsp;单&nbsp;号：</span>&nbsp;1610250035</td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">订单数量：</span>67米</td>
                            <td><span class="font-gray">辅助数量：</span>18米/卷</td>
                        </tr>
                        <tr class="gray">
                            <td colspan="2" class="red"><span class="font-gray">特殊要求：</span>标签贴在圈头,发韩国的料,包两层塑料</td>
                        </tr>
                        <tr>
                            <td><span class="font-gray">整料仓位：</span>A1001</td>
                            <td>
                                <span class="font-gray">整料数量：</span>
                                <input type="text" class="txt visible-text">卷
                            </td>
                        </tr>
                        <tr>
                            <td><span class="font-gray">零码仓位：</span>A1023</td>
                            <td>
                                <div class="pull-left">
                                    <span class="font-gray">零码数量：</span>
                                </div>
                                <div class="num pull-left">
                                    <table class="tab">
                                        <tr class="pull-left">
                                            <td>
                                                <input type="text" class="txt" name="remainder[{{id}}]" />
                                            </td>
                                            <td>
                                                <button onclick="myclick(this)" class="btn btn-default">
                                                    +
                                                </button>
                                                <span>米</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr class="yellow">
                            <td><span class="font-gray">标签数量：</span><span class="visible-num">4</span>张</td>
                            <td><span class="font-gray">分拣数量：</span><span class="fen-orange">65</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;米</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default center-block" data-dismiss="modal">确定分拣</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- 模态框（Modal）确定分拣 -->
    <div class="modal fade" id="sort" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
     <form action="confirmSort" method='post'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabe4">
                    分拣&nbsp;(<span class="title"></span>)
                </h4>
                </div>
                  <input type="hidden" value="" name="positionIds"/>
                  <input type="hidden" value="" name="orderProductId"/>
                  <input type="hidden" value="" name="unit"/>
                  <input type="hidden" value="" name="remark"/>
                   <input type="hidden" value="" name="singleNumber"/>
                <div class="modal-body">
                    <table class="table table-bordered import">
                        <tr class="gray">
                            <td class="col-lg-4"><span class="font-gray">分&nbsp;拣&nbsp;员：</span>&nbsp;<span class="packUser"></span></td>
                            <td class="col-lg-8"><span class="font-gray">发货仓库：</span><span class="warehouse"></span></td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">客户名称：</span><span class="name" ></span></td>
                            <td><span class="font-gray">订&nbsp;&nbsp;单&nbsp;号：</span>&nbsp;<span class="order"></span></td>
                        </tr>
                        <tr class="gray">
                            <td><span class="font-gray">订单数量：</span><span class="nums" ></span>米</td>
                            <td><span class="font-gray">辅助数量：</span><span class="unit" ></span>米/卷</td>
                        </tr>
                        <tr class="gray">
                            <td colspan="2" class="red"><span class="font-gray">特殊要求：</span><span class="memo" ></span></td>
                        </tr>
                        <tr>
                            <td><div class="col-sm-4"><span class="font-gray">整料仓位：</span></div>
                               <div class="col-sm-8">
                              <div class="warehouse-list">
                                <select class="form-control input-sm cate1">
                                <option value="default">请选择</option>
                              </select>
                              <select class="form-control input-sm cate2">
                                <option value="default">请选择</option>
                              </select>
                              <select class="form-control input-sm cate3">
                                <option value="default">请选择</option>
                              </select>
                              <input type="hidden" name="positionId[0]" value="" />
                              </div>
                              </div>
                            </td>
                            <td>
                                <span class="font-gray">整料数量：</span>
                                <input type="text" name="int" class="txt visible-text">卷
                            </td>
                        </tr>
                        <tr>
                            <td>  <div class="col-sm-4"> <span class="font-gray">零码仓位：</span></div>
                                  <div class="col-sm-8">
                                  <div class="warehouse-list">
                                    <select class="form-control input-sm cate1">
                                    <option value="default">请选择</option>
                                  </select>
                                  <select class="form-control input-sm cate2">
                                    <option value="default">请选择</option>
                                  </select>
                                  <select class="form-control input-sm cate3">
                                    <option value="default">请选择</option>
                                  </select>
                                  <input type="hidden" name="positionId[1]" value="" />
                                  </div>
                                  </div>
                            </td>
                            <td>
                                <div class="pull-left">
                                    <span class="font-gray">零码数量：</span>
                                </div>
                                <div class="num pull-left">
                                    <table class="tab ">
                                        <tr class="pull-left">
                                            <td>
                                                <input type="text" class="txt" name="remainder[0]"/>
                                            </td>
                                            <td>
                                                <button onclick="myclick(this)" class="btn btn-default">
                                                    +
                                                </button>
                                                <span>米</span>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr class="yellow">
                            <td><span class="font-gray">标签数量：</span><span class="visible-num">4</span>张</td>
                            <td><span class="font-gray">分拣数量：</span><span class="fen-orange">65</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;米</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-default center-block" >确定分拣</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal -->
        </form>
    </div>






  <script>seajs.use('/themes/classic/statics/app/warehouse/js/user.js');</script>
 <script>seajs.use('/themes/classic/statics/app/warehouse/js/bootstrap.min.js');</script>
   <script>seajs.use('/themes/classic/statics/app/warehouse/js/js/jquery.min.js');  </script>
    <script>seajs.use('/themes/classic/statics/app/warehouse/js/js/bootstrap.min.js');</script>
    <script>seajs.use('/themes/classic/statics/app/warehouse/js/js/sorting.js');</script>
    <script>seajs.use('/themes/classic/statics/app/warehouse/js/js/foundation-datepicker.min.js');</script>
     <script>seajs.use('/themes/classic/statics/app/warehouse/js/packingdetail.js');</script>
