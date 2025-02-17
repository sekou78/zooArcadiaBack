<?php

namespace App\Service;

use App\Document\AnimalConsultation;
use Doctrine\ODM\MongoDB\DocumentManager;

class ConsultationService
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function incrementConsultation(string $animalName): void
    {
        // Chercher l'animal dans la collection MongoDB
        $consultation = $this->dm->getRepository(AnimalConsultation::class)
            ->findOneBy(['animalName' => $animalName]);

        // Si l'animal n'existe pas encore, le créer
        if (!$consultation) {
            $consultation = new AnimalConsultation();
            $consultation->setAnimalName($animalName);
        }

        // Incrémenter le compteur de consultations
        $consultation->incrementConsultationCount();

        // Sauvegarder ou mettre à jour
        $this->dm->persist($consultation);
        $this->dm->flush();
    }

    public function getConsultationCount(string $animalName): int
    {
        // Récupérer le nombre de consultations pour l'animal
        $consultation = $this->dm->getRepository(AnimalConsultation::class)
            ->findOneBy(['animalName' => $animalName]);

        return $consultation ? $consultation->getConsultationCount() : 0;
    }
}
