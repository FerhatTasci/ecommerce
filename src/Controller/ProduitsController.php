<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\ProductCart;
use App\Entity\File;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\CartRepository;
use App\Repository\ProductCartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'products_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepo, CategoryRepository $categoryRepo, EntityManagerInterface $entityManager): Response
    {
        $params = $request->query->all();
        $selectedCategories = $params['categories'] ?? [];
        $priceMin = $params['priceMin'] ?? null;
        $priceMax = $params['priceMax'] ?? null;

        $groupedProducts = $productRepo->findProductsGroupedByCategory($selectedCategories, $priceMin, $priceMax);
        $categories = $categoryRepo->findAll();
        $images = $entityManager->getRepository(File::class)->findAll();

        return $this->render('product/index.html.twig', [
            'groupedProducts' => $groupedProducts,
            'categories' => $categories,
            'selectedCategories' => $selectedCategories,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'images' => $images,
        ]);
    }

    #[Route('/new', name: 'products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'products_show', methods: ['GET'])]
    public function show(Product $product, EntityManagerInterface $entityManager): Response
    {
        $images = $entityManager->getRepository(File::class)->findAll();
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'images' => $images,
        ]);
    }

    #[Route('/{id}/edit', name: 'products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'products_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if ($product) {
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/category/{id}', name: 'products_by_category', methods: ['GET'])]
    public function productsByCategory(Category $category, ProductRepository $productRepo): Response
    {
        $products = $productRepo->findBy(['category' => $category]);
        return $this->render('product/products_by_category.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'products_add_to_cart', methods: ['GET'])]
    public function addToCart(int $id, EntityManagerInterface $entityManager, ProductRepository $productRepo, CartRepository $cartRepo, ProductCartRepository $productCartRepo): Response
    {
        $user = $this->getUser();
        if ($user) {
            $product = $productRepo->find($id);
            $cart = $cartRepo->findOneBy(['user' => $user, 'save' => false]);

            if (!$cart) {
                $cart = new Cart();
                $cart->setUser($user);
                $cart->setTotal(0);
                $cart->setSave(false);
                $entityManager->persist($cart);
                $entityManager->flush();
            }

            $productCart = $productCartRepo->findOneBy(['cart' => $cart, 'product' => $product]);

            if (!$productCart) {
                $productCart = new ProductCart();
                $productCart->setProduct($product);
                $productCart->setQuantity(1);
                $productCart->setCart($cart);
                $cart->setTotal($cart->getTotal() + $product->getPriceHT());
                $entityManager->persist($productCart);
                $entityManager->flush();
            } else {
                $productCart->setQuantity($productCart->getQuantity() + 1);
                $entityManager->flush();
            }

            return $this->redirectToRoute('cart_show', ['id' => $cart->getId()]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/remove-from-cart/{productId}/{cartId}', name: 'products_remove_from_cart', methods: ['GET'])]
    public function removeFromCart(int $productId, int $cartId, EntityManagerInterface $entityManager, ProductRepository $productRepo, CartRepository $cartRepo, ProductCartRepository $productCartRepo): Response
    {
        $product = $productRepo->find($productId);
        $cart = $cartRepo->find($cartId);
        $productCart = $productCartRepo->findOneBy(['product' => $product, 'cart' => $cart]);

        if ($productCart) {
            $entityManager->remove($productCart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_show', ['id' => $cartId]);
    }
}
