<?php

declare(strict_types=1);

namespace App\Interfaces;

interface BlockRepositoryInterface
{
    public function index();

    public function getSingleIconBlock($id);

    public function delete($id);

    public function updateStatus($id, $status);

    public function duplicate($id);

    public function store($data);

    public function indexBlocks();

    public function productBlocks($data);

    public function cartBlocks();

    public function siteCommonBlocks();
}
