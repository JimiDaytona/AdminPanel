<?php

namespace App\Controller\Admin;

use App\Entity\Guests;
use App\Repository\TablesRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GuestsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Guests::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            BooleanField::new('presense'),
            'name',

            AssociationField::new('tableIn', 'Стол')
                ->setSortable(true)
                ->formatValue(fn ($value, $entity) => $entity->getTableIn()?->getNumber())
                ->onlyOnIndex(),
        ];
    }

    #[Route('/guests', name: 'guests', methods: ['GET'])]
    public function getGuests(TablesRepository $tablesRepository): JsonResponse
        {
            $guests = $tablesRepository->findAll();

            $data = array_map(fn ($guest) => [
                'id' => $guest->getId(),
                'name' => $guest->getName(),
                'presense' => $guest->isPresense(),
            ], $guests);

            return new JsonResponse($data);
        }

}
