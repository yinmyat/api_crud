<?php

namespace App\Repositories\Customer;

use App\Models\Customer;
use App\Api\Foundation\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\Storage;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function insertCustomer($data)
    {
        $img = $data['image'];

        $fileSize = $img->getSize();

        if ($fileSize > 0) {

            $fileName = time() . 'customer-img' . '.' . $img->extension();

            $img->storeAs('public/images/customer', $fileName);

            $path = 'storage/images/customer/' . $fileName;

            $data['image'] = $path;
        }
        else {
            $data['image'] = null;
        }

        $customer = $this->connection->query()->create($data);

        return $customer;

    }

    public function updateCustomer($data,$id)
    {
        $customer = $this->getDataById($id);

        if (!is_null($data['image'])) {

            $img = $data['image'];

            $fileName = time() . 'customer-img' . '.' . $img->extension();

            $img->storeAs('public/images/customer', $fileName);

            $path = 'storage/images/customer/' . $fileName;

            if (file_exists($customer->image)) {
                unlink($customer->image);
            }

            $data['image'] = $path;

        } else {
            unset($data['image']);
        }

        return  $this->connection->query()->where('id', $id)->update($data);
        
    }


}
