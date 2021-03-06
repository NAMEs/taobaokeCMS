@extends('admin.layouts.table.index')

@section('title', $title)
@section('headcss')

@stop
@section('content')

  @include('admin.layouts.table._tips')
<div class="row">
  <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>品牌分类</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <ul class="list-inline">
                  <li><a
                    @if($categoryId == 0) class="btn btn-danger" @else class="btn btn-info" @endif
                    href="{{ route('brands.index') }}">全部分类</a></li>
                  @foreach($brandCategorys as $brandCategory)
                  <li><a
                    @if($categoryId == $brandCategory->id) class="btn btn-danger" @else class="btn btn-info" @endif
                    href="{{ route('brands.index') }}?category={{ $brandCategory->id }}&page_size={{ $oldRequest['page_size'] or '' }}">{{ $brandCategory->name }}</a></li>
                  @endforeach
                </ul>
            </div>
        </div>
  </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>品牌列表</h5>
                <div class="ibox-tools">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-wrench"></i> 每页显示信息数
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="{{ route('brands.index') }}?category={{ $oldRequest['category'] or '' }}&page_size=10">每页显示10条信息</a></li>
                        <li><a href="{{ route('brands.index') }}?category={{ $oldRequest['category'] or '' }}&page_size=15">每页显示15条信息</a></li>
                        <li><a href="{{ route('brands.index') }}?category={{ $oldRequest['category'] or '' }}&page_size=20">每页显示20条信息</a></li>
                        <li><a href="{{ route('brands.index') }}?category={{ $oldRequest['category'] or '' }}&page_size=25">每页显示25条信息</a></li>
                        <li><a href="{{ route('brands.index') }}?category={{ $oldRequest['category'] or '' }}&page_size=30">每页显示30条信息</a></li>
                    </ul>
                </div>
            </div>
            <form action="" method="post" id="couponList">
            	{{ csrf_field() }}
              <div class="ibox-content" style="display: block;">
                  <div class="table-responsive">
                      <table class="table table-striped table-hover table-condensed">
                          <thead>
                              <tr>
                                  <th></th>
                                  <th>排序</th>
                                  <th style="min-width:75px;">品牌名称</th>
                                  <th class="text-center">商品总数</th>
                                  <th class="text-center">品牌分类</th>
                                  <th class="text-center">图片</th>
                                  <th class="text-center">品牌关键词</th>
                                  <th class="text-center">显示状态</th>
                                  <th class="text-center">操作</th>
                              </tr>
                          </thead>
                          <tbody id="chk">
                              @include('admin.brand._brand_info')
                          </tbody>
                      </table>
                      <table class="table table-striped">
                      	<tbody>
                      		<tr class="info">
                      			<td>
                      				<span type="" class="btn btn-xs btn-info"    onclick="chk(1)">全选  </span>
                      				<span type="" class="btn btn-xs btn-primary" onclick="chk(2)">反选  </span>
                      				<span type="" class="btn btn-xs btn-success" onclick="chk(3)">全不选</span>
                      				<span>|</span>
                      				<button type="submit" class="btn btn-xs btn-info"    onclick="submitChoice(1)"><i class="fa fa-close text-navy" style="color:#fff;"></i> 删除选中</button>
                      				<button type="submit" class="btn btn-xs btn-primary" onclick="submitChoice(2)"><i class="fa fa-hand-o-up text-navy" style="color:#fff;"></i> 修改排序</button>
                              <button type="submit" class="btn btn-xs btn-warning" onclick="submitChoice(3)"><i class="fa fa-hand-o-up text-navy" style="color:#fff;"></i> 一键更新商品总数</button>
                      			</td>
                      		</tr>
                      	</tbody>
                      </table>
                  </div>

                  <!-- 分页 -->
                  <div class="row text-center">
                      {!! $brands->appends($oldRequest)->render() !!}
                  </div>
              </div>
            </form>
        </div>
    </div>
</div>
@stop
@section('footJs')
<!-- 实现全选、反选、全不选 -->
<script type="text/javascript">
  function chk(value) {
    var chktotal = $("#chk");

    if (value == 1) { //全选
      $("input:checkbox").each(function () {
      this.checked = true;
      })
    }
    if (value == 2) { //反选
          $("input:checkbox").each(function () {
            this.checked = !this.checked;
         })
    }
    if (value == 3) { //全不选
      $("input:checkbox").removeAttr("checked");
    }
  }

</script>
<!-- 确定提交地址的js -->
<script type="text/javascript">
  function submitChoice(value) {
    var form = $("#couponList");

    if (value == 1) {
      form.action = '{{ route('brands.deleteMany') }}';
      $("#couponList").attr('action', form.action);
      form.submit();
    }
    if (value == 2) {
      form.action = "{{route('brands.changeOrder')}}";
      $("#couponList").attr('action', form.action);
      form.submit();
    }
    if (value == 3) {
      form.action = "{{route('brands.updateTotalMuti')}}";
      $("#couponList").attr('action', form.action);
      form.submit();
    }
  }

</script>
@stop
