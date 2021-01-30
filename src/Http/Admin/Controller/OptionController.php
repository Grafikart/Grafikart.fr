<?php

namespace App\Http\Admin\Controller;

use App\Domain\Live\LiveService;
use App\Helper\OptionManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OptionController extends BaseController
{
    public const MANAGEABLE_KEYS = ['spam_words', LiveService::OPTION_KEY];
    private OptionManagerInterface $optionManager;

    public function __construct(OptionManagerInterface $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    /**
     * @Route("/options", name="option_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/option/index.html.twig', [
            'menu' => 'option',
            'options' => $this->optionManager->all(self::MANAGEABLE_KEYS),
        ]);
    }

    /**
     * @Route("/options", methods={"POST"})
     */
    public function update(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;
        if (!in_array($key, self::MANAGEABLE_KEYS)) {
            throw new UnprocessableEntityHttpException('Impossible de modifier cette clef');
        }
        $this->optionManager->set($key, $value);

        return $this->json([]);
    }
}
