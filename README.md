# ci4_codeigniter4_api

## Buat Database dan Table
 
Disini saya membuat database dengan nama **restful_db**.
```sql
CREATE DATABASE restful_db;
```

#

Disini saya membuat sebuah table dengan nama **product**.
```sql
CREATE TABLE product(
product_id INT(11) PRIMARY KEY AUTO_INCREMENT,
product_name VARCHAR(200),
product_price DOUBLE
)ENGINE=INNODB;
```

#

Selanjutnya, insert beberapa data kedalam table **product** dengan mengeksekusi query berikut:
```sql
INSERT INTO product(product_name,product_price) VALUES
('Product 1','2000'),
('Product 2','5000'),
('Product 3','4000'),
('Product 4','6000'),
('Product 5','7000');
```

---

## Instalasi CodeIgniter 4 

Download file Codeigniter 4 pada link berikut:

[https://codeigniter.com](https://codeigniter.com)

Download dan ekstrack ke htdocs ubah folder dengan nama **ci4_codeigniter4_api**

---

## Membuat koneksi ke database

Buka file **Database.php** yang terdapat pada folder **app/Config**, kemudian temukan kode berikut:
```php
public $default = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'restful_db',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => (ENVIRONMENT !== 'production'),
    'cacheOn'  => false,
    'cacheDir' => '',
    'charset'  => 'utf8',
    'DBCollat' => 'utf8_general_ci',
    'swapPre'  => '',
    'encrypt'  => false,
    'compress' => false,
    'strictOn' => false,
    'failover' => [],
    'port'     => 3306,
];
```
Ubah **username** **password** **database**.

#

Agar Anda memiliki interface yang baik untuk menangani error, temukan file **env** pada root project, kemudian rename (ganti nama) menjadi **.env** dan open file tersebut.

Kemudian temukan kode berikut:
```php
# CI_ENVIRONMENT = production
```

Kemudian ubah menjadi seperti berikut:
```php
CI_ENVIRONMENT = development
```

---

## Membuat file Model

Buat sebuah file model bernama **ProductModel.php** pada folder **app/Models**, kemudian ketikan kode berikut:
```php
<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class ProductModel extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'product_id';
    protected $allowedFields = ['product_name','product_price'];
}
```

---

## Membuat file Controller

Buat sebuah file controller bernama **Products.php** pada folder **app/Controllers**, kemudian ketikan kode berikut:

```php
<?php namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductModel;
 
class Products extends ResourceController
{
    use ResponseTrait;
    // get all product
    public function index()
    {
        $model = new ProductModel();
        $data = $model->findAll();
        return $this->respond($data, 200);
    }
 
    // get single product
    public function show($id = null)
    {
        $model = new ProductModel();
        $data = $model->getWhere(['product_id' => $id])->getResult();
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }
    }
 
    // create a product
    public function create()
    {
        $model = new ProductModel();
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'product_price' => $this->request->getPost('product_price')
        ];
        $data = json_decode(file_get_contents("php://input"));
        //$data = $this->request->getPost();
        $model->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];
         
        return $this->respondCreated($response, 201);
    }
 
    // update product
    public function update($id = null)
    {
        $model = new ProductModel();
        $json = $this->request->getJSON();
        if($json){
            $data = [
                'product_name' => $json->product_name,
                'product_price' => $json->product_price
            ];
        }else{
            $input = $this->request->getRawInput();
            $data = [
                'product_name' => $input['product_name'],
                'product_price' => $input['product_price']
            ];
        }
        // Insert to Database
        $model->update($id, $data);
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => 'Data Updated'
            ]
        ];
        return $this->respond($response);
    }
 
    // delete product
    public function delete($id = null)
    {
        $model = new ProductModel();
        $data = $model->find($id);
        if($data){
            $model->delete($id);
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Data Deleted'
                ]
            ];
             
            return $this->respondDeleted($response);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }
    }
}
```
CodeIgniter 4 telah memberikan kemudahan bagi web developer dalam membuat RESTful API.

Dapat dilihat pada controller **Products.php** diatas, dengan hanya mengextends **ResourceController** kita telah dapat membuat RESTful API.

Tidak hanya itu,

Kita juga bisa dengan mudah membuat response dengan menggunakan **API ResponseTrait**.

---

## Konfigurasi Routes.php

Langkah terakhir yang tidak kalah pentingnya yaitu melakukan sedikit konfigurasi pada file Routes.php yang terdapat pada folder **app/Config**.

Buka file **Routes.php** pada folder **app/Config**, kemudian temukan kode berikut:

```php
$routes->get('/', 'Home::index');
$routes->resource('products');
```
**products** adalah nama controller

<p align="center">
  <img src="https://github.com/gzeinnumer/ci4_codeigniter4_api/blob/master/preview/example1.jpg"/>
</p>

---

## Aktifkan CORS (Cross-Origin Resources Sharing)

Agar resources dapat diakses di luar domain, kita perlu mengaktifkan CORS.
Untuk menaktifkan CORS, buat file bernama **Cors.php** pada folder **app/Filters**.
Kemudian ketikan kode berikut:

```php
<?php namespace App\Filters;
 
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
 
Class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
        die();
        }
    }
 
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
      // Do something here
    }
}
```

# 

Selanjutnya buka file Filters.php yang terdapat pada folder **app/Config**.
Kemudian temukan kode berikut:
```php
public $aliases = [
    'csrf'     => \CodeIgniter\Filters\CSRF::class,
    'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
    'honeypot' => \CodeIgniter\Filters\Honeypot::class,
];
```

Kemudian tambahkan cors filter seperti berikut:
```php
public $aliases = [
    'csrf'     => \CodeIgniter\Filters\CSRF::class,
    'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
    'honeypot' => \CodeIgniter\Filters\Honeypot::class,
    'cors'     => \App\Filters\Cors::class, 
];
```

Selanjutnya definisikan "cors" pada public globals seperti berikut:
```php
public $globals = [
    'before' => [
        'cors'
        //'honeypot'
        // 'csrf',
    ],
    'after'  => [
        'toolbar',
        //'honeypot'
    ],
];
```

---

## Testing

alankan project dengan mengetikkan perintah berikut pada Terminal / Command Prompt:
```
php spark serve
```
```
php spark serve --port 8081
```

#

If Error Happen
<p align="center">
  <img src="https://github.com/gzeinnumer/ci4_codeigniter4_api/blob/master/preview/example7.jpg"/>
</p>

#

Test On Postman

<p align="center">
  <img src="https://github.com/gzeinnumer/ci4_codeigniter4_api/blob/master/preview/example2.jpg"/>
</p>

<p align="center">
  <img src="https://github.com/gzeinnumer/ci4_codeigniter4_api/blob/master/preview/example3.jpg"/>
</p>

<p align="center">
  <img src="https://github.com/gzeinnumer/ci4_codeigniter4_api/blob/master/preview/example4.jpg"/>
</p>

<p align="center">
  <img src="https://github.com/gzeinnumer/ci4_codeigniter4_api/blob/master/preview/example5.jpg"/>
</p>

<p align="center">
  <img src="https://github.com/gzeinnumer/ci4_codeigniter4_api/blob/master/preview/example6.jpg"/>
</p>

---

Thanks to [mfikri.com](https://mfikri.com/artikel/restful-api-codeigniter4)

---

```
Copyright 2021 M. Fadli Zein
```