<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\ProductCart;
use App\Entity\File;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\ProductCartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index', methods: ['GET'])]
    public function index(Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = [];
        $total = 0;
    
        if ($user) {
            $cartEntity = $cartRepository->findOneBy(['user' => $user, 'save' => false]);
            if ($cartEntity) {
                foreach ($cartEntity->getProductCarts() as $productCart) {
                    $productId = $productCart->getProduct()->getId();
                    $cart[$productId] = [
                        'name' => $productCart->getProduct()->getName(),
                        'price' => $productCart->getProduct()->getPriceHT(),
                        'quantity' => $productCart->getQuantity()
                    ];
                    $total += $productCart->getProduct()->getPriceHT() * $productCart->getQuantity();
                }
            }
        } else {
            $session = $request->getSession();
            $cart = $session->get('cart', []);
            foreach ($cart as $id => $item) {
                $total += $item['price'] * $item['quantity'];
            }
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }


        #[Route('/new', name: 'cart_new', methods: ['GET', 'POST'])]
        public function new(Request $request, EntityManagerInterface $entityManager, CartRepository $cartRepository): Response
        {
            $user = $this->getUser();
            $userWithCart = $cartRepository->findOneBy(["user" => $user, "savedAt" => null]);
    
            if (!$userWithCart) {
                $cart = new Cart();
                $form = $this->createForm(CartType::class, $cart);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $cart->setUser($user);
                    $entityManager->persist($cart);
                    $entityManager->flush();
    
                    return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
                }
    
                return $this->render('cart/new.html.twig', [
                    'cart' => $cart,
                    'form' => $form,
                ]);
            } else {
                return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
            }
        }
    

    #[Route('/{id}', name: 'cart_show', methods: ['GET'])]
    public function show(Cart $cart, ProductCartRepository $productCartRepo): Response
    {
        $productCarts = $productCartRepo->findBy(['cart' => $cart]);

        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
            'productCarts' => $productCarts
        ]);
    }

    #[Route('/{id}/delete', name: 'cart_delete', methods: ['POST'])]
    public function delete(Request $request, ProductCartRepository $productCartRepo, CartRepository $cartRepo, EntityManagerInterface $entityManager): Response
    {
        $cart = $cartRepo->find($request->get('id'));

        if ($cart) {
            foreach ($cart->getProductCarts() as $productCart) {
                $entityManager->remove($productCart);
            }
            $entityManager->remove($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/validate', name: 'cart_validate', methods: ['GET', 'POST'])]
    public function validate(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        $cart->setSave(true);
        $entityManager->flush();

        return $this->redirectToRoute('cart_show', ['id' => $cart->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart', methods: ['GET'])]
    public function removeFromCart(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();

        if (!$user) {
            $cart = $session->get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
            }
            $session->set('cart', $cart);
        } else {
            $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user, 'save' => false]);
            $productCart = $entityManager->getRepository(ProductCart::class)->findOneBy(['cart' => $cart, 'product' => $id]);
            if ($productCart) {
                $entityManager->remove($productCart);
                $entityManager->flush();
            }
        }

        $this->addFlash('success', 'Produit supprimé du panier');
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/update-cart/{id}', name: 'update_cart', methods: ['POST'])]
    public function updateCart(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $quantity = $request->request->get('quantity', 0);
        $user = $this->getUser();
        $session = $request->getSession();

        if (!$user) {
            $cart = $session->get('cart', []);
            if (isset($cart[$id]) && $quantity > 0) {
                $cart[$id]['quantity'] = $quantity;
            } elseif ($quantity == 0) {
                unset($cart[$id]); 
            }
            $session->set('cart', $cart);
        } else {
            $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user, 'save' => false]);
            $productCart = $entityManager->getRepository(ProductCart::class)->findOneBy(['cart' => $cart, 'product' => $id]);
            if ($productCart) {
                if ($quantity > 0) {
                    $productCart->setQuantity($quantity);
                } else {
                    $entityManager->remove($productCart);
                }
                $entityManager->flush();
            }
        }

        $this->addFlash('success', 'Quantité mise à jour');
        return $this->redirectToRoute('cart_index');
    }
}
