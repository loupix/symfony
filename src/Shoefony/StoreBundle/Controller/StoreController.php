<?php

namespace Shoefony\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Shoefony\StoreBundle\Entity\Product;
use Shoefony\StoreBundle\Form\ProductType;
use Shoefony\StoreBundle\Entity\Opinion;
use Shoefony\StoreBundle\Form\OpinionType;

use Doctrine\ORM\Tools\Pagination\Paginator;

class StoreController extends Controller
{
    /**
     * @Route("/", name="shoefony_store_homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $brands = $em->getRepository("ShoefonyStoreBundle:Brand")->findAll();

        return $this->render('ShoefonyStoreBundle:Store:index.html.twig', Array("brands"=>$brands));
    }


    /**
     * @Route("/products", name="shoefony_store_products")
     */
    public function productsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository("ShoefonyStoreBundle:Product")->getList();
        $brands = $em->getRepository("ShoefonyStoreBundle:Brand")->findAll();

        return $this->render('ShoefonyStoreBundle:Store:products.html.twig', Array(
                "products"=>$products, 
                'brands'=>$brands,
                "brand"=>null,
                "col"=>4,
                "page"=>1));
    }





    /**
     * @Route("/products/page-{page}", name="shoefony_store_products_pages")
     */
    public function productsPageAction($page=0)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository("ShoefonyStoreBundle:Product")->getList($page);
        $brands = $em->getRepository("ShoefonyStoreBundle:Brand")->findAll();

        return $this->render('ShoefonyStoreBundle:Store:products.html.twig', Array(
                "products"=>$products, 
                "col"=>4,
                "brand"=>null,
                "page"=>$page,
                "brands"=>$brands));
    }









    
    public function partialProductsAction(Request $request, $col=4, $brand=null, $page=1)
    {
        $em = $this->getDoctrine()->getManager();
        if(!is_null($brand)){
            $products = $em->getRepository("ShoefonyStoreBundle:Product")->getListBrand($page, 4, $brand);
            $total_products = count($brand->getProducts());

        }else{
            $products = $em->getRepository("ShoefonyStoreBundle:Product")->getList($page);
            $total_products = $em->getRepository("ShoefonyStoreBundle:Product")
                            ->createQuerybuilder("p")
                            ->select("count(p.id)")
                            ->getQuery()->getSingleScalarResult();
        }

        $pagination = array('page'=>$page,'total_products'=>$total_products, "countPages"=>ceil($total_products/4));


        return $this->render('ShoefonyStoreBundle:Partial:products.html.twig', Array("products"=>$products, "col"=>$col, "pagination"=>$pagination));
    }


    public function partialOpinionsAction(Request $request, $product=null){
        $em = $this->getDoctrine()->getManager();
        $opinions = $product->getOpinions();
        $opinion = new Opinion();
        $form = $this->createForm(new OpinionType(), $opinion);


        return $this->render("ShoefonyStoreBundle:Partial:opinions.html.twig", Array("opinions"=>$opinions, "formOpinion"=>$form->createView()));
    }









    /**
     * @Route("/produit/{id}/details/{slug}", name="shoefony_store_produit", requirements={"id" = "\d+"})
     */
    public function produitDetailAction(Request $request, $id, $slug)
    {

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository("ShoefonyStoreBundle:Product")->find($id);
        
        $opinion = new Opinion();
        $form = $this->createForm(new OpinionType(), $opinion);
        
        if($request->isMethod("POST")){
            $form->bind($request);
            if($form->isValid()){
                $opinion = $form->getData();
                $product->addOpinions($opinion);
                $em->persist($opinion);
                $em->persist($product);
                $em->flush();
            }
        }



        
        if(is_null($product))
            throw $this->createNotFoundException('The product does not exist');
        $brands = $em->getRepository("ShoefonyStoreBundle:Brand")->findAll();
        return $this->render('ShoefonyStoreBundle:Store:product.html.twig', Array("product"=>$product, "brands"=>$brands));

    }





    /**
     * @Route("/brand/{id}/{slug}", name="shoefony_store_brand", requirements={"id" = "\d+"})
     */
    public function brandAction(Request $request, $id, $slug)
    {

        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository("ShoefonyStoreBundle:Brand")->find($id);
        $brands = $em->getRepository("ShoefonyStoreBundle:Brand")->findAll();

        if(is_null($brand))
            throw $this->createNotFoundException('The brand does not exist');

        return $this->render('ShoefonyStoreBundle:Store:products.html.twig', Array("brand"=>$brand, 'brands'=>$brands, 'page'=>1));

    }





    /**
     * @Route("/recherche", name="shoefony_store_recherche")
     */
    public function rechercheAction(Request $request){
        $product = new Product();
        $form = $this->createForm(new ProductType(), $product);

        if($request->isMethod("POST")){
            $form->bind($request);
            if($form->isValid()){
                $productForm = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $products = $em->getRepository("ShoefonyStoreBundle:Product")->findByTitle($productForm->getTitle());
                if(is_null($products))
                    throw $this->createNotFoundException('The products does not exist');

                $brands = $em->getRepository("ShoefonyStoreBundle:Brand")->findAll();


                return $this->render("ShoefonyStoreBundle:Store:recherche.html.twig", Array(
                    "brand"=>null,
                    "products"=>$products,
                    "brands"=>$brands));

            }
        }

        return $this->render("ShoefonyStoreBundle:Partial:recherche.html.twig", Array("form"=>$form->createView()));
    }
}
