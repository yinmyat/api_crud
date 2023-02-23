<?php

namespace App\Services\Customer;

use Exception;
use App\Repositories\Customer\CustomerRepository;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    protected $repo;
    public function __construct(CustomerRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllCustomer()
    {
        return $this->repo->getAll();
    }

    public function createCustomer($data)
    {

        DB::beginTransaction();
        try {
            $customer = $this->repo->insertCustomer($data);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            errorLogger($e->getMessage(), $e->getFile(), $e->getLine());
            return false;
        }

        return $customer;
    }

    public function customerUpdate($data, $id)
    {

        $customer = $this->repo->updateCustomer($data, $id);

        DB::beginTransaction();

        try {
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            errorLogger($e->getMessage(), $e->getFile(), $e->getLine());
            return false;
        }

        return $customer;
    }

    public function getCustomerByUuid($id)
    {
        return $this->repo->getDataByUuid($id);
    }

    public function deleteCustomer($uuid)
    {

        try {
            DB::beginTransaction();

            $customer = $this->getCustomerByUuid($uuid);

            $this->repo->destroy($customer->id);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            errorLogger($exception->getMessage(), $exception->getFile(), $exception->getLine());
            return 500;
        }

        return true;
    }

}
