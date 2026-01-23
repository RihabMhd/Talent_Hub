<?php
namespace App\Services;

use App\Repository\JobOfferRepository;

class JobOfferService
{
    private JobOfferRepository $jobOfferRepository;

    public function __construct(JobOfferRepository $jobOfferRepository)
    {
        $this->jobOfferRepository = $jobOfferRepository;
    }

    public function getAllJobOffers(): array
    {
        return $this->jobOfferRepository->findAll();
    }

    public function getJobOffersWithFilter(string $filter = 'all'): array
    {
        return $this->jobOfferRepository->findAllWithFilter($filter);
    }

    public function getJobOfferById(int $id): ?array
    {
        return $this->jobOfferRepository->findById($id);
    }

    public function createJobOffer(array $data): ?int
    {
        return $this->jobOfferRepository->create($data);
    }

    public function updateJobOffer(int $id, array $data): bool
    {
        return $this->jobOfferRepository->update($id, $data);
    }

    public function archiveJobOffer(int $id): bool
    {
        return $this->jobOfferRepository->softDelete($id);
    }

    public function restoreJobOffer(int $id): bool
    {
        return $this->jobOfferRepository->restore($id);
    }

    public function deleteJobOffer(int $id): bool
    {
        return $this->jobOfferRepository->delete($id);
    }

    public function getCompanyStats(): array
    {
        return $this->jobOfferRepository->getStatsByCompany();
    }
}