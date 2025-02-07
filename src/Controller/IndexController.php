<?php

namespace App\Controller;

use App\Service\ApiService;
use App\Service\ExcelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ApiService $sApi): Response
    {
        $prospects = $sApi->getProspects();
        return $this->render('index/index.html.twig', [
            'prospects' => $prospects,
        ]);
    }

    #[Route('/add-prospect', name: 'app_new_prospect')]
    public function new(Request $request, ApiService $sApi): Response
    {
        $params = $request->request->all();
        $prospectData = [
            'id' => 0,
            'nom' => $params['nom'],
            'prenom' => $params['prenom'],
            'telephone' => $params['telephone'],
            'mail' => $params['mail'],
            'niveauActuel' => $params['niveauActuel'],
            'diplomePrepare' => $params['diplomePrepare'],
            'specialite' => $params['specialite'],
            'etablissement' => $params['etablissement'],
            'codePostal' => $params['codePostal'],
            'ville' => $params['ville'],
            'anneeRecrutement' => (int)$params['anneeRecrutement'],
            'entreeEn' => $params['entreeEn'],
            'commentaire' => $params['commentaire'],
            'anneeRecrutement' => $params['anneeRecrutement'],
            'createdAt' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
        ];

        $sApi->addProspect($prospectData);
        return $this->redirectToRoute('app_index');
    }

    #[Route('/edit-prospect/{id}', name: 'app_edit_prospect')]
    public function edit(int $id, Request $request, ApiService $sApi): Response
    {
        $params = $request->request->all();
        $prospectData = [
            'id' => $id,
            'nom' => $params['nom'],
            'prenom' => $params['prenom'],
            'telephone' => $params['telephone'],
            'mail' => $params['mail'],
            'niveauActuel' => $params['niveauActuel'],
            'diplomePrepare' => $params['diplomePrepare'],
            'specialite' => $params['specialite'],
            'etablissement' => $params['etablissement'],
            'codePostal' => $params['codePostal'],
            'ville' => $params['ville'],
            'anneeRecrutement' => (int)$params['anneeRecrutement'],
            'entreeEn' => $params['entreeEn'],
            'commentaire' => $params['commentaire'],
            'anneeRecrutement' => $params['anneeRecrutement'],
            'createdAt' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
        ];

        $sApi->updateProspect($id, $prospectData);
        return $this->redirectToRoute('app_index');
    }

    #[Route('/delete-prospect/{id}', name: 'app_delete_prospect')]
    public function delete(int $id, ApiService $sApi): Response
    {
        $sApi->deleteProspect($id);
        return $this->redirectToRoute('app_index');
    }

    // #[Route('/export-prospect', name: 'app_application_export_prospect')]
    // public function exportProspectToExcel(ExcelService $excelService, ApiService $sApi): Response
    // {
    //     $prospects = $sApi->getProspects();
    //     return $excelService->exportProspectToExcel($prospects);
    // }
}