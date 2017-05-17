#  LARAVEL - ACR FİLE -- FİLE UPLOAD CLASS

[Query-File-Upload](https://github.com/blueimp/jQuery-File-Upload): Paketi refarans alarak oluşturulmuştur.

## Kurulum:
#### composer json : 
```
"acr/file": "dev-menus"
```
### CONFİG

#### Providers
```
Acr\Menu\AcrMenuServiceProviders::class
```
#### Aliases
```
'AcrMenu'      => Acr\Menu\Facades\AcrMenu::class
```
### app\Http\Middleware Admin.php

```php 
PHP
namespace App\Http\Middleware;

use Acr\Menu\Model\AcrUser;
use Closure;
use Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_model = new AcrUser();
        $isAdmin    = $user_model->where('id', Auth::user()->id)->with([
            'roles' => function ($query) {
                $query->where('role_id', 1);
            }
        ])->first();
        // dd($isAdmin->roles->count());
        if ($isAdmin->roles->count() == 0 || $isAdmin->roles[0]->id != 1) {
            return redirect('/yetkisiz');
        }
        return $next($request);
    }
}

```
acr_file_id: ilişkili tablodan gelmeli örneğin ürünler için kullanacaksanız urun tablonuzda acr_file_id stunu olmalı, acr_file_id değişkeni null gelirse : $acr_file_id = AcrMenu::create($acr_file_id) yeni bir acr_file_id oluşturur.
```php 
PHP
 echo AcrMenu::acr_sol_menu(@$tab);  
```

```sql 
Mysql Tablosu

CREATE TABLE `acrmenus` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `sabit` tinyint(4) DEFAULT '0',
  `goster` int(11) NOT NULL DEFAULT '1',
  `ozet` tinytext COLLATE utf8_turkish_ci,
  `main` int(11) DEFAULT '0',
  `panel` int(11) DEFAULT NULL,
  `kat` int(11) DEFAULT '1',
  `aitKat` int(11) DEFAULT NULL,
  `aciklama` tinytext COLLATE utf8_turkish_ci,
  `anahtarKelimeler` text COLLATE utf8_turkish_ci,
  `yeri` tinyint(4) DEFAULT NULL,
  `sira` int(11) DEFAULT '10',
  `yorum` tinyint(4) DEFAULT '1',
  `name` varchar(250) COLLATE utf8_turkish_ci DEFAULT NULL,
  `class` varchar(60) COLLATE utf8_turkish_ci DEFAULT NULL,
  `kisaBaslik` varchar(250) COLLATE utf8_turkish_ci DEFAULT NULL,
  `uzunBaslik` varchar(250) COLLATE utf8_turkish_ci DEFAULT NULL,
  `yazi` longtext COLLATE utf8_turkish_ci,
  `acilisTipi` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `link` varchar(1000) COLLATE utf8_turkish_ci DEFAULT NULL,
  `seoLink` varchar(250) COLLATE utf8_turkish_ci DEFAULT NULL,
  `bagla` varchar(250) COLLATE utf8_turkish_ci DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

ALTER TABLE `acrmenus`
  ADD UNIQUE KEY `menuID` (`id`);

ALTER TABLE `acrmenus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
```

