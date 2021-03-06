<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Home\BaseController;
use App\Models\CouponCategory;
use App\Models\Coupon;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\Category;
use App\Traits\CouponCategorySelfWhere;
use App\Traits\ShowFromToView;

class BrandController extends BaseController
{
  use CouponCategorySelfWhere, ShowFromToView;

  public $pageSize = 16;

  // 优惠券分类的列表
  public function index(Request $request)
  {
    $oldRequest = $request->all();
    $currentUrl = $request->url();
    $id = empty($request->id) ? 0 : $request->id;
    $from = self::$from;
    $TDK = ['title'=>'优惠券商品品牌 | '.config('website.name'),
            'keywords'=>'',
            'description'=>''];

    $AllBrandCategorys = BrandCategory::AllBrandCategorys();
    $categorys = Category::categorys(self::$from);

    if (self::$from == 'pc') {
      $brands = $this->brandsPC($id, $AllBrandCategorys);
      $couponsRecommend = Coupon::couponsRecommendRandom(self::$from, 12);
      return view('home.pc.brand.index', compact('oldRequest',
                                                 'id',
                                                 'currentUrl',
                                                 'from',
                                                 'TDK',
                                                 'AllBrandCategorys',
                                                 'couponsRecommend',
                                                 'brands',
                                                 'categorys'
                                               ));
    } else {
      $brands = $this->brandsWX($id, 18);
      $show_from = $this->showFrom(self::$from);
      $couponCategorys = CouponCategory::couponCategorys(self::$from);
      $couponsGussYouLike = Coupon::couponsRecommendRandom(self::$from, 5, 4);
      return view('home.wx.brand.index', compact('oldRequest',
                                                 'show_from',
                                                 'id',
                                                 'currentUrl',
                                                 'from',
                                                 'TDK',
                                                 'AllBrandCategorys',
                                                 'brands',
                                                 'couponsGussYouLike',
                                                 'categorys',
                                                 'couponCategorys'
                                               ));
    }
  }

  // 获取无线端优惠券信息
  public function brandsWX ($id, $pageSize = 20)
  {
    $brands = Brand::where('is_show', 1)
                    ->where('total', '>', 0);

    if ( $id != 0) {
      $brands = $brands->where('brand_category_id', $id);
    }

    return $brands->orderBy('order', 'asc')
                  ->paginate($pageSize);

  }

  // 获取PC端优惠券信息
  public function brandsPC ($id, $AllBrandCategorys)
  {
    $brandsArr = [];

    if ( $id != 0) {
      $brandCategory = BrandCategory::find($id);
      $brandsArr[0]['brand_category_name'] = $brandCategory->name;
      $brandsArr[0]['id'] = $brandCategory->id;
      $brandsArr[0]['brands'] = Brand::where('is_show', 1)
                                      ->where('total', '>', 0)
                                      ->where('brand_category_id', $id)
                                      ->orderBy('order', 'asc')
                                      ->get();
    } else {
      foreach ($AllBrandCategorys as $key => $AllBrandCategory) {
        $brandsArr[$key]['brand_category_name'] = $AllBrandCategory->name;
        $brandsArr[$key]['id'] = $AllBrandCategory->id;
        $brandsArr[$key]['brands'] = Brand::where('is_show', 1)
                                            ->where('total', '>', 0)
                                            ->where('brand_category_id', $AllBrandCategory->id)
                                            ->take(6)
                                            ->orderBy('order', 'asc')
                                            ->get();
      }
    }

    unset($brands);

    return $brandsArr;
  }

  // 展示品牌对应的优惠的列表
  public function brandCoupons(Request $request)
  {
    $oldRequest = $request->all();
    $currentUrl = $request->url();
    $from = self::$from;
    $coupons = $this->coupons($request, $this->pageSize);
    $brand = $this->brandInfo($request);
    $TDK = ['title'=>$brand->name.'优惠券商品 | '.config('website.name'),
            'keywords'=>'',
            'description'=>''];
    $categorys = Category::categorys(self::$from);
    $show_from = $this->showFrom(self::$from);

    if (self::$from == 'pc') {
      $currentBrand = Brand::find($request->id);
      $currentBrandCategory = BrandCategory::find($currentBrand->brand_category_id);
      $brandCategoryList = Brand::where('brand_category_id', $currentBrand->brand_category_id)->where('total','>', 0)->get();
      // $AllBrandCategorys = BrandCategory::AllBrandCategorys();
      $couponsRecommend = Coupon::couponsRecommendRandom(self::$from, 6);
      return view('home.pc.brand.coupon', compact('oldRequest',
                                                  'show_from',
                                                  'currentUrl',
                                                  'from',
                                                  'TDK',
                                                  'id',
                                                  'brand',
                                                  'coupons',
                                                  'couponsRecommend',
                                                  'currentBrand',
                                                  'currentBrandCategory',
                                                  'brandCategoryList',
                                                  // 'AllBrandCategorys',
                                                  'categorys'
                                                ));
    } else {
      $couponCategorys = CouponCategory::couponCategorys(self::$from);
      $couponsGussYouLike = Coupon::couponsRecommendRandom(self::$from, 5, 4);
      return view('home.wx.couponCategory.index', compact('oldRequest',
                                                          'show_from',
                                                          'currentUrl',
                                                          'from',
                                                          'TDK',
                                                          'brand',
                                                          'coupons',
                                                          'couponsGussYouLike',
                                                          'categorys',
                                                          'couponCategorys'
                                                        ));
    }
  }

  // 获取优惠券信息
  public function coupons (Request $request, $pageSize = 20)
  {
    $coupons = new Coupon;

    if ( !empty($request->id) ) {
      $keywords = Brand::find($request->id)->keywords;
      $coupons = $coupons->where('goods_name', 'like', $keywords);
    }

    $coupons = $coupons->where('coupon_last', '>', 0);
    $coupons = $this->couponOrderBy($coupons, $request->order);

    return $coupons->paginate($pageSize);
  }

  // 优惠券的排序
  public function couponOrderBy ($coupons, $order)
  {
    switch ($order) {
      case 'sales_down':
        $coupons = $coupons->orderBy('sales', 'desc');
        break;

      case 'sales_up':
        $coupons = $coupons->orderBy('sales', 'asc');
        break;

      case 'rate_down':
        $coupons = $coupons->orderBy('rate_sales', 'desc');
        break;

      case 'rate_up':
        $coupons = $coupons->orderBy('rate_sales', 'asc');
        break;

      case 'price_now_down':
        $coupons = $coupons->orderBy('price_now', 'desc');
        break;

      case 'price_now_up':
        $coupons = $coupons->orderBy('price_now', 'asc');
        break;

      case 'taobao':
        $coupons = $coupons->orderBy('flat', 'asc');
        break;

      case 'tmall':
        $coupons = $coupons->orderBy('flat', 'desc');
        break;

      default:
        $coupons = $coupons->orderBy('id', 'desc');
        break;
    }

    return $coupons;
  }

  // 获取品牌信息
  public function brandInfo($request)
  {
    if (!empty($request->id)) {
      return Brand::find($request->id);
    }

    return null;
  }
}
