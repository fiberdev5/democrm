<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\HomeSliderController;
use App\Http\Controllers\Backend\SiteSettingsController;
use App\Http\Controllers\Backend\CompanySettingsController;
use App\Http\Controllers\Backend\SocialMediaSettingController;
use App\Http\Controllers\Backend\HomeCardController;
use App\Http\Controllers\Backend\MenusController;
use App\Http\Controllers\Backend\ClientsController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\PagesController;
use App\Http\Controllers\Backend\EmailSetingsController;
use App\Http\Controllers\Backend\GoogleSettingController;
use App\Http\Controllers\Backend\HomeKayitController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\AboutController;
use App\Http\Controllers\Backend\RoomController;
use App\Http\Controllers\Backend\GalleryController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\ReferencesController;
use App\Http\Controllers\Backend\RoomImageController;
use App\Http\Controllers\Backend\RoomFacilityController;
use App\Http\Controllers\Backend\DocumentsController;
use App\Http\Controllers\Backend\MisyonController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProductImageController;
use App\Http\Controllers\Backend\PrivacyPolicyController;
use App\Http\Controllers\Backend\CategoriesController;
use App\Http\Controllers\Backend\CategoryImagesController;
use App\Http\Controllers\Backend\FeatureImagesController;
use App\Http\Controllers\Backend\FeaturesController;
use App\Http\Controllers\Backend\PricingController;
use App\Http\Controllers\Frontend\CarController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Frontend\DeviceBrandsController;
use App\Http\Controllers\Frontend\DeviceTypesController;
use App\Http\Controllers\Frontend\FeatureController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\HakkimizdaController;
use App\Http\Controllers\Frontend\ProductsController;
use App\Http\Controllers\Frontend\KatalogController;
use App\Http\Controllers\Frontend\FrontendContactController;
use App\Http\Controllers\Frontend\GenelAyarlarController;
use App\Http\Controllers\Frontend\OfferController;
use App\Http\Controllers\Frontend\PaymentMethodsController;
use App\Http\Controllers\Frontend\PaymentTypesController;
use App\Http\Controllers\Frontend\PersonelController;
use App\Http\Controllers\Frontend\ReceiptDesignController;
use App\Http\Controllers\Frontend\RoleController;
use App\Http\Controllers\Frontend\ServiceFormSetController;
use App\Http\Controllers\Frontend\ServiceResourceController;
use App\Http\Controllers\Frontend\ServicesController;
use App\Http\Controllers\Frontend\ServiceStagesController;
use App\Http\Controllers\Frontend\ServiceTimeController;
use App\Http\Controllers\Frontend\StageQuestionController;
use App\Http\Controllers\Frontend\StockCategoryController;
use App\Http\Controllers\Frontend\StockShelfController;
use App\Http\Controllers\Frontend\StockSupplierController;
use App\Http\Controllers\Frontend\StockController;
use App\Http\Controllers\Frontend\WarrantyPeriodController;


Route::get('/secure', function () {
    return view('backend.index');
})->middleware(['auth', 'verified'])->name('dashboard');

//Admin login logout routes
Route::controller(UserController::class)->group(function () {
    //Route::get('/register', 'register')->name('register');
    //Route::post('/register', 'register_action')->name('register.action');

    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'login_action')->name('login.action');
});


Route::middleware(['auth'])->group(function () {

    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/logout', 'destroy')->name('admin.logout');
        Route::get('/admin/profile', 'Profile')->name('admin.profile');
        Route::get('/edit/profile', 'EditProfile')->name('edit.profile');
        Route::post('/store/profile', 'StoreProfile')->name('store.profile');
    });
});

//Anasayfa Slider
Route::middleware(['auth'])->group(function () {
    Route::controller(HomeSliderController::class)->group(function () {
        Route::get('/home/image', 'HomeImage')->name('home.image');
        Route::get('/add/slide', 'AddSlide')->name('add.slide');
        Route::post('/store/slide', 'StoreSlide')->name('store.slide');
        Route::get('/edit/slide/{id}', 'EditSlide')->name('edit.slide');
        Route::post('/update/slide', 'UpdateSlide')->name('update.slide');
        Route::get('/delete/slide/{id}', 'DeleteSlide')->name('delete.slide');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(AboutController::class)->group(function () {
        Route::get('/all/about', 'AllAbout')->name('all.about');
        Route::post('/update/about', 'UpdateAbout')->name('update.about');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(PrivacyPolicyController::class)->group(function () {
        Route::get('/all/privacy', 'AllPrivacy')->name('all.privacy');
        Route::post('/update/privacy', 'UpdatePrivacy')->name('update.privacy');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(MisyonController::class)->group(function () {
        Route::get('/all/misyon', 'AllMisyon')->name('all.misyon');
        Route::post('/update/misyon', 'UpdateMisyon')->name('update.misyon');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/site/settings', 'SiteSettings')->name('site.settings');
        Route::post('/update/site/settings', 'UpdateSiteSettings')->name('update.site.settings');

        Route::get('/email/settings', 'EmailSettings')->name('email.settings');
        Route::post('/update/email/settings', 'UpdateEmailSettings')->name('update.email.settings');

        Route::get('/google/settings', 'GoogleSettings')->name('google.settings');
        Route::post('/update/google/settings', 'UpdateGoogleSettings')->name('update.google.settings');

        Route::get('/company/settings', 'CompanySettings')->name('company.settings');
        Route::post('/update/company/settings', 'UpdateCompanySettings')->name('update.company.settings');

        Route::get('/social/media/settings', 'SocialMediaSettings')->name('social.media.settings');
        Route::post('/update/socialmedia/settings', 'UpdateSocialMediaSettings')->name('update.socialmedia.settings');
    });
});



//Anasayfa servislerimiz bölümü
Route::middleware(['auth'])->group(function () {
    Route::controller(HomeCardController::class)->group(function () {
        Route::get('/all/home/card', 'AllHomeCard')->name('all.home.card');
        Route::get('/add/home/card', 'AddHomeCard')->name('add.home.card');
        Route::post('/store/home/card', 'StoreHomeCard')->name('store.home.card');
        Route::get('/edit/home/card/{id}', 'EditHomeCard')->name('edit.home.card');
        Route::post('/update/home/card', 'UpdateHomeCard')->name('update.home.card');
        Route::get('/delete/home/card/{id}', 'DeleteHomeCard')->name('delete.home.card');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(RoomController::class)->group(function () {
        Route::get('/all/project', 'AllRoom')->name('all.room');
        Route::get('/add/project', 'AddRoom')->name('add.room');
        Route::post('/store/project', 'StoreRoom')->name('store.room');
        Route::get('/edit/project/{id}', 'EditRoom')->name('edit.room');
        Route::post('/update/project', 'UpdateRoom')->name('update.room');
        Route::get('/delete/project/{id}', 'DeleteRoom')->name('delete.room');
    });
});

Route::middleware(['auth'])->group(function() {
    Route::controller(CategoriesController::class)->group(function() {
        Route::get('/all/categories', 'AllCategories')->name('all.categories');
        Route::get('/add/categories', 'AddCategories')->name('add.categories');
        Route::post('/store/categories', 'StoreCategories')->name('store.categories');
        Route::get('/edit/categories/{id}', 'EditCategories')->name('edit.categories');
        Route::post('/update/categories', 'UpdateCategories')->name('update.categories');
        Route::get('/delete/categories/{id}', 'DeleteCategories')->name('delete.categories');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(CategoryImagesController::class)->group(function () {
        Route::get('/all/category/image/{id}', 'AllCategoryImage')->name('all.category.image');
        Route::get('/add/category/image/{id}', 'AddCategoryImage')->name('add.category.image');
        Route::post('/store/category/image', 'StoreCategoryImage')->name('store.category.image');
        Route::get('/delete/category/image/{id}', 'DeleteCategoryImage')->name('delete.category.image');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(MenusController::class)->group(function () {
        Route::get('/all/menus', 'AllMenus')->name('all.menus');
        Route::get('/add/menus', 'AddMenus')->name('add.menus');
        Route::post('/store/menus', 'StoreMenus')->name('store.menus');
        Route::get('/edit/menus/{id}', 'EditMenus')->name('edit.menus');
        Route::post('/update/menus/{id}', 'UpdateMenus')->name('update.menus');
        Route::get('/delete/menus/{id}', 'DeleteMenus')->name('delete.menus');
    });
});

//Şirket müşteri yorumları kısmı
Route::middleware(['auth'])->group(function () {
    Route::controller(ClientsController::class)->group(function () {
        Route::get('/all/client', 'AllClient')->name('all.client');
        Route::get('/add/client', 'AddClient')->name('add.client');
        Route::post('/store/client', 'StoreClient')->name('store.client');
        Route::get('/edit/client/{id}', 'EditClient')->name('edit.client');
        Route::post('/update/client', 'UpdateClient')->name('update.client');
        Route::get('/delete/client/{id}', 'DeleteClient')->name('delete.client');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(GalleryController::class)->group(function () {
        Route::get('/all/gallery', 'AllGallery')->name('all.gallery');
        Route::get('/add/gallery', 'AddGallery')->name('add.gallery');
        Route::post('/store/gallery', 'StoreGallery')->name('store.gallery');
        Route::post('/store/sort', 'StoreSort')->name('store.sort');
        Route::get('/edit/gallery/{id}', 'EditGallery')->name('edit.gallery');
        Route::post('/update/gallery', 'UpdateGallery')->name('update.gallery');
        Route::get('/delete/gallery/{id}', 'DeleteGallery')->name('delete.gallery');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(FaqController::class)->group(function () {
        Route::get('/all/faq', 'AllFaq')->name('all.faq');
        Route::get('/add/faq', 'AddFaq')->name('add.faq');
        Route::post('/store/faq', 'StoreFaq')->name('store.faq');
        Route::get('/edit/faq/{id}', 'EditFaq')->name('edit.faq');
        Route::post('/update/faq', 'UpdateFaq')->name('update.faq');
        Route::get('/delete/faq/{id}', 'DeleteFaq')->name('delete.faq');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(ReferencesController::class)->group(function () {
        Route::get('/all/references', 'AllReferences')->name('all.references');
        Route::get('/add/references', 'AddReferences')->name('add.references');
        Route::post('/store/references', 'StoreReferences')->name('store.references');
        Route::post('/store/references/sort', 'StoreReferencesSort')->name('store.references.sort');
        Route::get('/edit/references/{id}', 'EditReferences')->name('edit.references');
        Route::post('/update/references', 'UpdateReferences')->name('update.references');
        Route::get('/delete/references/{id}', 'DeleteReferences')->name('delete.references');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(RoomImageController::class)->group(function () {
        Route::get('/all/project/image/{id}', 'AllRoomImage')->name('all.room.image');
        Route::get('/add/project/image/{id}', 'AddRoomImage')->name('add.room.image');
        Route::post('/store/project/image', 'StoreRoomImage')->name('store.room.image');
        Route::get('/edit/project/image/{id}', 'EditRoomImage')->name('edit.room.image');
        Route::post('/update/project/image', 'UpdateRoomImage')->name('update.room.image');
        Route::get('/delete/project/image/{id}', 'DeleteRoomImage')->name('delete.room.image');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(RoomFacilityController::class)->group(function () {
        Route::get('/all/room/facility', 'AllRoomFacility')->name('all.room.facility');
        Route::get('/add/room/facility', 'AddRoomFacility')->name('add.room.facility');
        Route::post('/store/room/facility', 'StoreRoomFacility')->name('store.room.facility');
        Route::get('/edit/room/facility/{id}', 'EditRoomFacility')->name('edit.room.facility');
        Route::post('/update/room/facility', 'UpdateRoomFacility')->name('update.room.facility');
        Route::get('/delete/room/facility/{id}', 'DeleteRoomFacility')->name('delete.room.facility');
    });
});

//İletişim sayfası
Route::middleware(['auth'])->group(function () {
    Route::controller(ContactController::class)->group(function () {
        Route::get('/contact/message', 'ContactMessage')->name('contact.message');
        Route::get('/delete/message/{id}', 'DeleteMessage')->name('delete.message');
    });
});

//Sayfa ayarları
Route::middleware(['auth'])->group(function () {
    Route::controller(PagesController::class)->group(function () {
        Route::get('/pages', 'Pages')->name('pages');
        Route::get('/pages/home', 'PagesHome')->name('pages.home');
        Route::post('/update/pages/home', 'UpdatePagesHome')->name('update.pages.home');

        Route::get('/pages/about', 'PagesAbout')->name('pages.about');
        Route::post('/update/pages/about', 'UpdatePagesAbout')->name('update.pages.about');

        Route::get('/pages/projects', 'PagesRooms')->name('pages.rooms');
        Route::post('/update/pages/projects', 'UpdatePagesRoom')->name('update.pages.rooms');

        Route::get('/pages/gallery', 'PagesGallery')->name('pages.gallery');
        Route::post('/update/pages/gallery', 'UpdatePagesGallery')->name('update.pages.gallery');

        Route::get('/pages/contact', 'PagesContact')->name('pages.contact');
        Route::post('/update/pages/contact', 'UpdatePagesContact')->name('update.pages.contact');

        Route::get('/pages/products', 'PagesProducts')->name('pages.products');
        Route::post('/update/pages/products', 'UpdatePagesProducts')->name('update.pages.products');

        Route::get('/pages/misyon', 'PagesMisyon')->name('pages.misyon');
        Route::post('/update/pages/misyon', 'UpdatePagesMisyon')->name('update.pages.misyon');

        Route::get('/pages/references', 'PagesReferences')->name('pages.references');
        Route::post('/update/pages/references', 'UpdatePagesReferences')->name('update.pages.references');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('/all/product', 'AllProduct')->name('all.product');
        Route::get('/add/product', 'AddProduct')->name('add.product');
        Route::post('/store/product', 'StoreProduct')->name('store.product');
        Route::get('/edit/product/{id}', 'EditProduct')->name('edit.product');
        Route::post('/update/product', 'UpdateProduct')->name('update.product');
        Route::get('/delete/product/{id}', 'DeleteProduct')->name('delete.product');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(ProductImageController::class)->group(function () {
        Route::get('/all/product/image/{id}', 'AllProductImage')->name('all.product.image');
        Route::get('/add/product/image/{id}', 'AddProductImage')->name('add.product.image');
        Route::post('/store/product/image', 'StoreProductImage')->name('store.product.image');
        Route::get('/edit/product/image/{id}', 'EditProductImage')->name('edit.product.image');
        Route::post('/update/product/image', 'UpdateProductImage')->name('update.product.image');
        Route::get('/delete/product/image/{id}', 'DeleteProductImage')->name('delete.product.image');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(DocumentsController::class)->group(function () {
        Route::get('/all/documents', 'AllDocuments')->name('all.documents');
        Route::post('/update/documents', 'UpdateDocuments')->name('update.documents');
        Route::get('/delete/documents/{id}', 'DeleteDocuments')->name('delete.documents');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(PricingController::class)->group(function () {
        Route::get('/all/pricing', 'AllPricing')->name('all.pricing');
        Route::get('/add/pricing', 'AddPricing')->name('add.pricing');
        Route::post('/store/pricing', 'StorePricing')->name('store.pricing');
        Route::get('/edit/pricing/{id}', 'EditPricing')->name('edit.pricing');
        Route::post('/update/pricing', 'UpdatePricing')->name('update.pricing');
        Route::get('/delete/pricing/{id}', 'DeletePricing')->name('delete.pricing');
    });
});

Route::middleware(['auth'])->group(function() {
    Route::controller(FeaturesController::class)->group(function() {
        Route::get('/all/features', 'AllFeatures')->name('all.features');
        Route::get('/add/features', 'AddFeatures')->name('add.features');
        Route::post('/store/features', 'StoreFeatures')->name('store.features');
        Route::get('/edit/features/{id}', 'EditFeatures')->name('edit.features');
        Route::post('/update/features', 'UpdateFeatures')->name('update.features');
        Route::get('/delete/features/{id}', 'DeleteFeatures')->name('delete.features');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::controller(FeatureImagesController::class)->group(function () {
        Route::get('/all/features/image/{id}', 'AllFeaturesImage')->name('all.features.image');
        Route::get('/add/features/image/{id}', 'AddFeaturesImage')->name('add.features.image');
        Route::post('/store/features/image', 'StoreFeaturesImage')->name('store.features.image');
        Route::get('/edit/features/image/{id}', 'EditFeaturesImage')->name('edit.features.image');
        Route::post('/update/features/image', 'UpdateFeaturesImage')->name('update.features.image');
        Route::get('/delete/features/image/{id}', 'DeleteFeaturesImage')->name('delete.features.image');
    });
});

Route::controller(HomeController::class)->group(function() {
    Route::get('/', 'Index')->name('home');
    Route::get('/pricing', 'Pricing')->name('pricing');

    Route::get('/kullanici-kaydi', 'Register')->name('kayit');
    Route::post('/register-action', 'RegisterAction')->name('kayit.action');

    Route::get('/kullanici-girisi', 'Login')->name('giris');
    Route::post('/login-action', 'LoginAction')->name('giris.action');

    Route::get('/logout', 'logout')->name('logout');
    Route::get('/get-states/{countryId}', 'getStatesByCountry')->name('get.states');

});

Route::group(['prefix' => '{tenant_id}', 'middleware' => ['auth','checkTenantId']], function () {
    Route::controller(HomeController::class)->group(function() {
        Route::get('/dashboard', 'Dashboard')->name('secure.home');
    });

    Route::controller(PersonelController::class)->group(function() {
        Route::get('/personeller', 'AllStaffs')->name('staffs');
        Route::get('/personel-ekle', 'AddStaff')->name('add.staff');
        Route::post('/personel-gonder', 'StoreStaff')->name('store.staff');
        Route::get('/personel/duzenle/{id}', 'EditStaff')->name('edit.staff');
        Route::post('/personel/guncelle/{id}', 'UpdateStaff')->name('update.personel');
        Route::get('/personel/sil/{id}', 'DeleteStaff')->name('delete.personel');

        //Dealer Routes
        Route::get('/bayiler', 'AllDealers')->name('dealers');
        Route::get('/bayi-ekle', 'AddDealer')->name('add.dealer');
        Route::post('/bayi-kaydet', 'StoreDealer')->name('store.dealer');
        Route::get('/bayi/duzenle/{id}', 'EditDealer')->name('edit.dealer');
        Route::post('/bayi/guncelle/{id}', 'UpdateDealer')->name('update.dealer');
        Route::get('/bayi-sil/{id}', 'DeleteDealer')->name('delete.dealer');
        Route::get('/bayiler/data', 'GetDealersData')->name('dealers.data');

    });
    
    Route::controller(StockController::class)->group(function() {
        Route::get('/stoklar', 'AllStocks')->name('stocks');
        Route::get('/stoklar/data', 'GetStocksAjax')->name('stocks.ajax');
        Route::get('/stok-ekle', 'AddStock')->name('add.stock');
        Route::post('/stok-kaydet', 'StoreStock')->name('store.stock'); 
        Route::get('/stok/duzenle/{id}', 'EditStock')->name('edit.stock');
        Route::post('/stok/guncelle/{id}', 'UpdateStock')->name('update.stock');
    });


    Route::controller(CustomerController::class)->group(function() {
        Route::get('/musteriler', 'AllCustomer')->name('customers');
        Route::get('/musteri-ekle', 'AddCustomer')->name('add.customer');
        Route::post('/musteri-gonder', 'StoreCustomer')->name('store.customer');
        Route::get('/musteri/duzenle/{id}', 'EditCustomer')->name('edit.customer');
        Route::post('/musteri/guncelle/{id}', 'UpdateCustomer')->name('update.customer');
        Route::get('/musteri/sil/{id}', 'DeleteCustomer')->name('delete.customer');
        Route::get('/musteri-servisleri/{id}', 'CustomerServices')->name('customer.services');
    });

    Route::controller(OfferController::class)->group(function() {
        Route::get('/teklifler', 'AllOffer')->name('offers');
        Route::get('/teklif-ekle', 'AddOffer')->name('add.offer');
        Route::post('/teklif-kaydet', 'StoreOffer')->name('store.offer');
        Route::get('/teklif-duzenle/{id}', 'EditOffer')->name('edit.offer');
        Route::post('/teklif-guncelle', 'UpdateOffer')->name('update.offer');
        Route::get('/teklif-sil/{id}', 'DeleteOffer')->name('delete.offer');
        Route::get('/teklif-yazdir/{id}', 'OffertoPdf')->name('offer.pdf');
    });

    //GENEL AYARLAR MODÜLÜ
    Route::controller(GenelAyarlarController::class)->group(function() {
        Route::get('/genel-ayarlar', 'GeneralSettings')->name('general.settings');
        Route::get('/firma-bilgileri', 'CompanySettings')->name('firma.settings');
        Route::post('/firma-ayari/guncelle', 'UpdateCompanySet')->name('update.firma');
        Route::get('/sms-ayarlari', 'SmsSettings')->name('sms.settings');
        Route::post('/sms-ayari/guncelle', 'UpdateSms')->name('update.sms');
    });

    Route::controller(DeviceBrandsController::class)->group(function() {
        Route::get('/cihaz-markalari', 'DeviceBrands')->name('device.brands');
        Route::get('/cihaz-ekle', 'AddDevice')->name('add.device');
        Route::post('/cihaz-yukle', 'StoreDevice')->name('store.device');
        Route::get('/cihaz-duzenle/{id}', 'EditDevice')->name('edit.device');
        Route::post('/cihaz-guncelle', 'UpdateDevice')->name('update.device');
        Route::delete('/cihaz-sil/{id}', 'DeleteDevice')->name('delete.device');
    });

    Route::controller(DeviceTypesController::class)->group(function() {
        Route::get('/cihaz-turleri', 'DeviceTypes')->name('device.types');
        Route::get('/cihaz-turu/ekle', 'AddDeviceType')->name('add.device.type');
        Route::post('/cihaz-turu/yukle', 'StoreDeviceType')->name('store.device.type');
        Route::get('/cihaz-turu/duzenle/{id}', 'EditDeviceType')->name('edit.device.type');
        Route::post('/cihaz-turu/guncelle', 'UpdateDeviceType')->name('update.device.type');
        Route::delete('/cihaz-turu/sil/{id}', 'DeleteDeviceType')->name('delete.device.type');
    });

    Route::controller(WarrantyPeriodController::class)->group(function() {
        Route::get('/garanti-sureleri', 'WarrantyPeriods')->name('warranty.period');
        Route::get('/garanti-ekle', 'AddWarrantyPeriod')->name('add.warranty');
        Route::post('/garanti-yukle', 'StoreWarrantyPeriod')->name('store.warranty');
        Route::get('/garanti-duzenle/{id}', 'EditWarrantyPeriod')->name('edit.warranty');
        Route::post('/garanti-guncelle', 'UpdateWarrantyPeriod')->name('update.warranty');
        Route::delete('/garanti-sil/{id}', 'DeleteWarrantyPeriod')->name('delete.warranty');
    });

    Route::controller(CarController::class)->group(function() {
        Route::get('/araclar', 'AllCars')->name('all.cars');
        Route::get('/arac-ekle', 'AddCar')->name('add.car');
        Route::post('/arac-yukle', 'StoreCar')->name('store.car');
        Route::get('/arac-duzenle/{id}', 'EditCar')->name('edit.car');
        Route::post('/arac-guncelle', 'UpdateCar')->name('update.car');
        Route::delete('/arac-sil/{id}', 'DeleteCar')->name('delete.car');
    });

    Route::controller(ServiceStagesController::class)->group(function() {
        Route::get('/servis-asamalari', 'AllServiceStage')->name('service.stages');
        Route::get('/servis-asama/ekle', 'AddServiceStage')->name('add.service.stage');
        Route::post('/servis-asama/yukle', 'StoreServiceStage')->name('store.service.stage');
        Route::get('/servis-asama/duzenle/{id}', 'EditServiceStage')->name('edit.service.stage');
        Route::post('/servis-asama/guncelle', 'UpdateServiceStage')->name('update.service.stage');
        Route::delete('/servis-asama/sil/{id}', 'DeleteServiceStage')->name('delete.service.stage');
    });

    Route::controller(ServiceTimeController::class)->group(function() {
        Route::get('/servis-zamanlama', 'ServiceTime')->name('service.time');
        Route::post('/servis-zamani/yukle', 'UpdateServiceTime')->name('update.service.time');
    
        
    });

    Route::controller(ServiceResourceController::class)->group(function() {
        Route::get('/servis-kaynaklari', 'AllServiceResource')->name('service.resources');
        Route::get('/servis-kaynak/ekle', 'AddServiceResource')->name('add.service.resource');
        Route::post('/servis-kaynak/yukle', 'StoreServiceResource')->name('store.service.resource');
        Route::get('/servis-kaynak/duzenle/{id}', 'EditServiceResource')->name('edit.service.resource');
        Route::post('/servis-kaynak/guncelle', 'UpdateServiceResource')->name('update.service.resource');
        Route::delete('/servis-kaynak/sil/{id}', 'DeleteServiceResource')->name('delete.service.resource');
    });

    Route::controller(StageQuestionController::class)->group(function() {
        Route::get('/servis-asama-sorulari', 'AllStageQuestions')->name('all.stage.questions');
        Route::get('/servis-asama-sorusu/ekle', 'AddStageQuestion')->name('add.stage.question');
        Route::post('/servis-asama-sorusu/yukle', 'StoreStageQuestion')->name('store.stage.question');
        Route::get('/servis-asama-sorusu/duzenle/{id}', 'EditStageQuestion')->name('edit.stage.question');
        Route::post('/servis-asama-sorusu/guncelle', 'UpdateStageQuestion')->name('update.stage.question');
        Route::delete('/servis-asama-sorusu/sil/{id}', 'DeleteStageQuestion')->name('delete.stage.question');
        
        Route::get('/stage-questions/get', 'getStageQuestions')->name('get.stage.questions');
    });

    
    Route::controller(RoleController::class)->group(function () {
        Route::get('/izinler', 'AllPermission')->name('all.permission');
        Route::get('/izin/ekle', 'AddPermission')->name('add.permission');
        Route::post('/izin/gonder', 'StorePermission')->name('store.permission');
        Route::get('/izin/duzenle/{id}', 'EditPermission')->name('edit.permission');
        Route::post('/izin/guncelle', 'UpdatePermission')->name('update.permission');
        Route::delete('/izin/sil/{id}', 'DeletePermission')->name('delete.permission');
    });
    
    Route::controller(RoleController::class)->group(function () {
        Route::get('/roller', 'AllRoles')->name('all.roles');
        Route::get('/rol/ekle', 'AddRoles')->name('add.roles');
        Route::post('/rol/gonder', 'StoreRoles')->name('store.roles');
        Route::get('/rol/duzenle/{id}', 'EditRoles')->name('edit.roles');
        Route::post('/rol/guncelle', 'UpdateRoles')->name('update.roles');
        Route::delete('/rol/sil/{id}', 'DeleteRoles')->name('delete.roles');


        Route::get('/rollere/izin/ekle', 'AddRolesPermission')->name('add.roles.permission');
        Route::post('/rollere/izin/kaydet', 'StoreRolesPermission')->name('store.roles.permission');
        Route::get('/rollerdeki/izinler', 'AllRolesPermission')->name('all.roles.permission');
        Route::get('/rollerdeki/izinleri/duzenle/{id}', 'EditRolesPermission')->name('edit.roles.permission');
        Route::post('/rollerdeki/izinleri/guncelle/{id}', 'UpdateRolesPermission')->name('update.roles.permission');
        Route::get('/rollerdeki/izinleri/sil/{id}', 'DeleteRolesPermission')->name('delete.roles.permission');
    });


    Route::controller(StockCategoryController::class)->group(function() {
        Route::get('/stok-kategorileri', 'AllStockCategory')->name('stock.categories');
        Route::get('/stok-kategori/ekle', 'AddStockCategory')->name('add.stock.category');
        Route::post('/stok-kategori/yukle', 'StoreStockCategory')->name('store.stock.category');
        Route::get('/stok-kategori/duzenle/{id}', 'EditStockCategory')->name('edit.stock.category');
        Route::post('/stok-kategori/guncelle', 'UpdateStockCategory')->name('update.stock.category');
        Route::delete('/stok-kategori/sil/{id}', 'DeleteStockCategory')->name('delete.stock.category');
    });

    Route::controller(StockShelfController::class)->group(function() {
        Route::get('/stok-raflari', 'AllStockShelf')->name('stock.shelves');
        Route::get('/stok-raf/ekle', 'AddStockShelf')->name('add.stock.shelf');
        Route::post('/stok-raf/yukle', 'StoreStockShelf')->name('store.stock.shelf');
        Route::get('/stok-raf/duzenle/{id}', 'EditStockShelf')->name('edit.stock.shelf');
        Route::post('/stok-raf/guncelle', 'UpdateStockShelf')->name('update.stock.shelf');
        Route::delete('/stok-raf/sil/{id}', 'DeleteStockShelf')->name('delete.stock.shelf');
    });

    Route::controller(StockSupplierController::class)->group(function() {
        Route::get('/stok-tedarikcileri', 'AllStockSupplier')->name('stock.suppliers');
        Route::get('/stok-tedarikci/ekle', 'AddStockSupplier')->name('add.stock.supplier');
        Route::post('/stok-tedarikci/yukle', 'StoreStockSupplier')->name('store.stock.supplier');
        Route::get('/stok-tedarikci/duzenle/{id}', 'EditStockSupplier')->name('edit.stock.supplier');
        Route::post('/stok-tedarikci/guncelle', 'UpdateStockSupplier')->name('update.stock.supplier');
        Route::delete('/stok-tedarikci/sil/{id}', 'DeleteStockSupplier')->name('delete.stock.supplier');
    });


    Route::controller(PaymentMethodsController::class)->group(function() {
        Route::get('/odeme-sekilleri', 'AllPaymentMethods')->name('payment.methods');
        Route::get('/odeme-sekli/ekle', 'AddPaymentMethod')->name('add.payment.method');
        Route::post('/odeme-sekli/yukle', 'StorePaymentMethod')->name('store.payment.method');
        Route::get('/odeme-sekli/duzenle/{id}', 'EditPaymentMethod')->name('edit.payment.method');
        Route::post('/odeme-sekli/guncelle', 'UpdatePaymentMethod')->name('update.payment.method');
        Route::delete('/odeme-sekli/sil/{id}', 'DeletePaymentMethod')->name('delete.payment.method');
    });

    Route::controller(PaymentTypesController::class)->group(function() {
        Route::get('/odeme-turleri', 'AllPaymentTypes')->name('payment.types');
        Route::get('/odeme-turu/ekle', 'AddPaymentType')->name('add.payment.type');
        Route::post('/odeme-turu/yukle', 'StorePaymentType')->name('store.payment.type');
        Route::get('/odeme-turu/duzenle/{id}', 'EditPaymentType')->name('edit.payment.type');
        Route::post('/odeme-turu/guncelle', 'UpdatePaymentType')->name('update.payment.type');
        Route::delete('/odeme-turu/sil/{id}', 'DeletePaymentType')->name('delete.payment.type');
    });

    Route::controller(ServiceFormSetController::class)->group(function() {
        Route::get('/servis-form/ayarlari', 'ServiceFormSettings')->name('service.form.settings');
        Route::post('/servis-form/guncelle', 'UpdateServiceFormSettings')->name('update.service.form.settings');
    });

    Route::controller(ReceiptDesignController::class)->group(function() {
        Route::get('/yazici-fis/tasarimi', 'ReceiptDesign')->name('receipt.design');
        Route::post('/fis-tasarimi/guncelle', 'UpdateReceiptDesign')->name('update.receipt.design');
    });

    //SERVİSLER MODÜLÜ
    Route::controller(ServicesController::class)->group(function() {
        Route::get('/servisler', 'AllServices')->name('all.services');
        Route::get('/servis/ekle', 'AddService')->name('add.service');
        Route::post('/servis/yukle', 'StoreService')->name('store.service');
        Route::get('/servis/duzenle/{id}', 'EditService')->name('edit.service');
        Route::post('/servis/guncelle', 'UpdateService')->name('update.service');
        Route::delete('/servis/sil/{id}', 'DeleteService')->name('delete.service');

        Route::post('/customer/search', 'searchCustomer')->name('customer.search');

        Route::get('/servis-bilgileri/tum/{id}', 'TumServiceDesc')->name('tum.service.desc');
        Route::get('/servis-musteri/duzenle/{id}', 'EditServiceCustomer')->name('edit.service.customer');
        Route::get('/servis-asama-sorusu-getir/{asamaid}/{serviceid}', 'ServiceStageQuestionShow')->name('service.stage.question.show');
        Route::post('/servis-plan-kaydet', 'SaveServicePlan')->name('save.service.plan');
        Route::get('/servis-asama/{id}/history', 'getServiceStageHistory')->name('service.stage.history');
        Route::post('/servis-plan-sil/{planid}', 'DeleteServicePlan')->name('delete.service.plan');
        Route::get('/servis-plan/duzenle/{planid}', 'EditServicePlan')->name('edit.service.plan');
        Route::post('/servis-plan/guncelle', 'UpdateServicePlan')->name('update.service.plan');
        
        //servis yazdırma
        Route::get('/servis-yazdir/{id}', 'ServicetoPdf')->name('serviceto.pdf');

        //servis para hareketleri
        Route::get('/servis-para-hareketleri/{service_id}', 'ServiceMoneyActions')->name('service.money.actions');
        Route::get('/servis-gelir-ekle/{service_id}', 'AddServiceIncome')->name('add.service.income');
        Route::post('/servis-gelir-kaydet', 'StoreServiceIncome')->name('store.service.income');
        Route::get('/servis-gider-ekle/{service_id}', 'AddServiceExpense')->name('add.service.expense');
        Route::post('/servis-gider-kaydet', 'StoreServiceExpense')->name('store.service.expense');
        Route::get('/servis-para-hareketi/duzenle/{service_id}', 'EditServiceMoneyAction')->name('edit.service.money.action');
        Route::post('/servis-para-hareketi/guncelle', 'UpdateServiceMoneyAction')->name('update.service.money.action');
        Route::get('/servis-para-hareketi/sil/{service_id}', 'DeleteServiceMoneyAction')->name('delete.service.money.action');
    });
});






























Route::controller(HakkimizdaController::class)->group(function() {
    Route::get('/about', 'About')->name('about');
});

Route::controller(ProductsController::class)->group(function() {
    Route::get('/usage/areas', 'index')->name('products');
    Route::get('/usage/areas/{slug}', 'UrunDetails' )->name('product.details');
    Route::get('/urun/{slug}', 'Products')->name('products.alt');
});

Route::controller(KatalogController::class)->group(function() {
    Route::get('/kataloglar', 'index')->name('katalogs');
});

Route::controller(FrontendContactController::class)->group(function() {
    Route::get('/contact', 'index')->name('contact');
    Route::post('/store/message', 'StoreMessage')->name('store.message');
});

Route::controller(FeatureController::class)->group(function() {
    Route::get('/features', 'Features')->name('features');
    Route::get('/features/{slug}', 'FeatureDetails' )->name('feature.details');

});

