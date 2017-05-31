<?php
namespace Maginx\AlsoOrderedProduct\Api;

use Maginx\AlsoOrderedProduct\Api\Data\AlsoOrderedInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface AlsoOrderedRepositoryInterface
{
    public function save(AlsoOrderedInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(AlsoOrderedInterface $page);

    public function deleteById($id);
}
