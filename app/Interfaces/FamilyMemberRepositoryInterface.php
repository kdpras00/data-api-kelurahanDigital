<?php
namespace App\Interfaces;

interface FamilyMemberRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        ?string $relation = null
    );

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage,
        ?string $relation = null
    );

    public function getById(
        string $id
    );
    public function create(array $data);

    public function update(
        string $id,
        array $data
    );

    public function delete(
        string $id
    );
}
