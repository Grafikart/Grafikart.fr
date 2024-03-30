<?php

namespace App\Http\Admin\Controller;

use App\Domain\Coupon\Entity\Coupon;
use App\Http\Admin\Data\CouponCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: "/coupon", name:"coupon_")]
class CouponController extends CrudController
{
    protected string $templatePath = 'coupon';
    protected string $menuItem = 'coupon';
    protected string $entity = Coupon::class;
    protected string $routePrefix = 'admin_coupon';
    protected string $searchField = 'name';

    #[Route(path: "/", name:"index")]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: "/new", name:"new")]
    public function new(): Response
    {
        $coupon = new Coupon();
        $data = new CouponCrudData($coupon);

        return $this->crudNew($data);
    }

    #[Route(path: "/{id}", name: "delete", methods: ["DELETE"])]
    public function delete(Coupon $coupon): Response
    {
        return $this->crudDelete($coupon);
    }

    #[Route(path: "/{id}", name: "edit")]
    public function edit(Coupon $coupon): Response
    {
        $data = new CouponCrudData($coupon);

        return $this->crudEdit($data);
    }
}
