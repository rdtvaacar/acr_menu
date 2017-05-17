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
### app\Http\Middleware 

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
 echo AcrMenu::css();  
```
CSS dosyalarını yükler.
```php 
PHP
echo AcrMenu::form()
```
Formu yükler
```php 
PHP
echo AcrMenu::js($acr_file_id)
```
Java script dosylarını yükler.

```sql 
Mysql Tablosu

CREATE TABLE `acr_files` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(66) COLLATE utf8_turkish_ci DEFAULT NULL,
  `file_dir` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sil` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `acr_files_childs`
--

CREATE TABLE `acr_files_childs` (
  `id` int(11) NOT NULL,
  `acr_file_id` int(11) DEFAULT NULL,
  `file_name` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `file_name_org` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `fize_size` varchar(25) COLLATE utf8_turkish_ci DEFAULT NULL,
  `file_type` varchar(10) COLLATE utf8_turkish_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sil` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

ALTER TABLE `acr_files`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `acr_files_childs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `acr_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `acr_files_childs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
```
Dosya yolu  /acr_files/acr_file_id
