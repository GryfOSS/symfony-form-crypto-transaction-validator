<?php

declare(strict_types=1);

use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoTransactionHash;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class TestController extends AbstractController
{
    /**
     * Combined form for testing both address and transaction validation
     */
    public function cryptoForm(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('crypto_type', ChoiceType::class, [
                'choices' => [
                    'Tron' => 'tron',
                    'Ethereum' => 'ethereum',
                ],
                'required' => true,
                'label' => 'Cryptocurrency Type'
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'label' => 'Crypto Address'
            ])
            ->add('transaction_hash', TextType::class, [
                'required' => false,
                'label' => 'Transaction Hash'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Validate'])
            ->getForm();

        $form->handleRequest($request);

        $result = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $result = [
                    'status' => 'success',
                    'message' => 'All validations passed',
                    'data' => $data
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => []
                ];

                // Collect all form errors
                foreach ($form->getErrors(true) as $error) {
                    $result['errors'][] = $error->getMessage();
                }
            }
        }

        return $this->render('crypto_form.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }

    /**
     * Dedicated address validation form
     */
    public function addressForm(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('crypto_type', ChoiceType::class, [
                'choices' => [
                    'Tron' => 'tron',
                    'Ethereum' => 'ethereum',
                ],
                'required' => true,
                'label' => 'Cryptocurrency Type'
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'label' => 'Crypto Address'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Validate Address'])
            ->getForm();

        $form->handleRequest($request);

        $result = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $result = [
                    'status' => 'success',
                    'message' => 'Address is valid',
                    'data' => $data
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'Address validation failed',
                    'errors' => []
                ];

                foreach ($form->getErrors(true) as $error) {
                    $result['errors'][] = $error->getMessage();
                }
            }
        }

        return $this->render('address_form.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }

    /**
     * Dedicated transaction validation form
     */
    public function transactionForm(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('crypto_type', ChoiceType::class, [
                'choices' => [
                    'Tron' => 'tron',
                    'Ethereum' => 'ethereum',
                ],
                'required' => true,
                'label' => 'Cryptocurrency Type'
            ])
            ->add('transaction_hash', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'label' => 'Transaction Hash'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Validate Transaction'])
            ->getForm();

        $form->handleRequest($request);

        $result = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $result = [
                    'status' => 'success',
                    'message' => 'Transaction is valid',
                    'data' => $data
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'Transaction validation failed',
                    'errors' => []
                ];

                foreach ($form->getErrors(true) as $error) {
                    $result['errors'][] = $error->getMessage();
                }
            }
        }

        return $this->render('transaction_form.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }
}