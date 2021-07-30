<?php namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductModel;
 
class QueryTest extends ResourceController
{

    public function funGet()
    {
        $response = [
            'status'   => 200,
            'data'    => 'data'
        ];
        return $this->respond($response);
    }

    public function funPost()
    {
        $json = $this->request->getJSON();
        $response = [
            'status'   => 200,
            'data'    => $json
        ];
        return $this->respond($response);
    }

    public function funPostParams($id = null)
    {
        $response = [
            'status'   => 200,
            'data'    => $id
        ];
        return $this->respond($response);
    } 
    
    public function query()
    {
        $query = "SELECT * FROM product;";
        $db = \Config\Database::connect();
        $data = $db->query($query);

        // return json_encode($data->getResult());
        // return json_encode($query->getResultArray());
        // return json_encode($query->getRow());
        return $this->respond($data->getResult());
    }
}