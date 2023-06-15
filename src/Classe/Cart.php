<?php
 
namespace App\Classe;
 
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
 
 
class Cart {
    private $session;
    private $entityManager;
    public function __construct(EntityManagerInterface $entitymanager, RequestStack $stack) {
        $this->session = $stack->getSession();
        $this->entityManager = $entitymanager;
    }

    public function add($id) {
        // Je stocke la session actuelle du panier dans la variable $cart qui renvoie un tableau
        $cart = $this->session->get('cart', []);

        // Si le panier a déjà le produit avec l'ID spécifié
        if (!empty($cart[$id])) {
            // Alors j'ajoute une quantité supplémentaire
            $cart[$id]++;
        } else {
            // Sinon, j'ajoute le produit avec une quantité de 1
            $cart[$id] = 1;
        }

        // Je mets à jour le panier dans la session
        $this->session->set('cart', $cart);
    }

    public function get() {
        // Je récupère le contenu du panier depuis la session
        return $this->session->get('cart');
    }

    public function remove() {
        // Je supprime le panier de la session
        return $this->session->remove('cart');
    }

    public function delete($id) 
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);
    }

    public function getfull($stack)
    {
        $cartComplete = [];

        foreach($stack->getSession()->get('cart') as $id => $quantity)
        {
            $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
            if (!$product_object) {
                $this->delete($id);
                continue;
            }
            $cartComplete[] = [
                'product' => $product_object,
                'quantity' => $quantity
            ];
        }
        return $cartComplete;
    }
}