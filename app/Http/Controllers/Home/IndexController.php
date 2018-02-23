<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Home\SourceOfAccessController;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\Coupon;

class IndexController extends SourceOfAccessController
{
    public function index (Request $request)
    {
      $TDK = ['title'=>'网站首页',
              'keywords'=>'',
              'description'=>''];

      $banners = Banner::banners(self::$from);
      $categorys = Category::categorys(self::$from);
      $brandCategorys = BrandCategory::brandCategorys(self::$from);
      $brands = Brand::brands(self::$from, $brandCategorys);
      $coupons = Coupon::couponsRecommendRandom(self::$from);
      $from = self::$from;

      if (self::$from === 'pc') {
        return view('home.wx.index.index', compact('TDK', 'banners', 'categorys', 'brandCategorys', 'brands', 'coupons', 'from'));
      } else {
        return view('home.wx.index.index', compact('TDK', 'banners', 'categorys', 'brandCategorys', 'brands', 'coupons', 'from'));
      }
    }
}