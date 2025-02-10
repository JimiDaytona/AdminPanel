<?php

namespace App\Controller\Admin;

use App\Entity\Tables;
use App\Repository\TablesRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TablesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tables::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            'number',
            'description',
            'maxPeople',

            IntegerField::new('GuestsInTable', 'Гостей')
                ->onlyOnIndex(),

            AssociationField::new('guests', 'Присутствующие гости')
                ->formatValue(function ($value) {
                    $presentGuests = $value->filter(fn ($guest) => $guest->isPresense());
                    return $presentGuests->count();
                })
                ->onlyOnIndex(),

        ];
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Tables) {
            return;
        }
        if (!$entityInstance->getGuests()->isEmpty()) {
            $this->addFlash('warning', 'Невозможно удалить стол: за ним есть гости.');
            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    #[Route('/tableNum/{id}', name: 'by_Number', methods: ['GET'])]
    public function getByNumber(int $id, TablesRepository $tablesRepository): JsonResponse
    {
        $table = $tablesRepository->findBy(['number' => $id ]);
        $data = array_map(fn ($table) => [
            'id' => $table->getId(),
            'number' => $table->getNumber(),
            'description' => $table->getDescription(),
        ], $table);

        return new JsonResponse($data);
    }

    #[Route('/tables/{id}/guests', name: 'guests_in_table', methods: ['GET'])]
    public function getGuestsInTable(int $id, TablesRepository $tablesRepository): JsonResponse
    {
        $table = $tablesRepository->find($id);

        if (!$table) {
            return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $guests = $table->getGuests();
        foreach ($guests as $guest) {
            $guestData = [
                'id' => $guest->getId(),
                'name' => $guest->getName(),
            ];
        }

        return new JsonResponse([
            'table_id' => $table->getId(),
            'number' => $table->getNumber(),
            'guests' => $guestData,
        ]);
    }

    #[Route('/tables_stats', name: 'tables_stats', methods: ['GET'])]
    public function getTablesStats(TablesRepository $tablesRepository): JsonResponse
    {
        $tables = $tablesRepository->findAll();
        foreach ($tables as $table) {
            $tablesData = [
                'id' => $table->getId(),
                'number' => $table->getNumber(),
                'description' => $table->getDescription(),
                'max_people' => $table->getMaxPeople(),
                'bookings' => $table->getGuestsInTable(),
                'guestIsPresent' => $this->PresenseGuets($table->getGuests()),
            ];

        return new JsonResponse($tablesData);

        }
    }

    private function PresenseGuets($value)
    {
        $presentGuests = $value->filter(fn ($guest) => $guest->isPresense());
        return $presentGuests->count();
    }
}
