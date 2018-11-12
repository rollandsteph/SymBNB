<?php 
namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService{

    private $entityClass;
    private $limit=10;
    private $currentPage=1;
    private $manager;
    private $route;
    private $twig;
    private $templatePath;

    /**
     * récupère le manager pour pouvoir utiliser le repository
     * puis récupère l'environment twig pour pouvoir injecter via la méthode display
     * du contenu twig dans une page twig
     *
     * @param ObjectManager $manager
     * @param Environment $twig
     */
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath){
        $this->manager=$manager;
        $this->twig=$twig;
        // on peut récupérer des informations de la request et notamment la route et tout autre paramètre
        $this->setRoute($request->getCurrentRequest()->attributes->get('_route'));
        $this->setCurrentPage($request->getCurrentRequest()->attributes->get('page'));
        $this->setTemplatePath($templatePath);
    }

    /**
     * Permet de renvoyer l'ensemble des enregistrements à afficher
     * en fonction de l'offset, de la limit ...
     *
     * @return void
     */
    public function getData(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entity sur laquelle nous devons paginer !
            Utilisez la méthode setEntityClass de votre objet PaginationService.");
        }
        // calculer l'offset
        $offset=$this->currentPage * $this->limit - $this->limit;

        // faire la recherche des éléments
        $repo=$this->manager->getRepository($this->entityClass);
        // argument 1 : les critères de recherche
        // argument 2 : les critères de tri
        // argument 3 : le nombre d'enregistrement
        // argument 4 : l'offset (à partir de quel enregistrement)
        $data=$repo->findBy([],[],$this->limit,$offset);

        return $data;
    }

    /**
     * Permet de renvoyer le rendu twig de la pagination
     *
     * @return void
     */
    public function getTwigPagination(){

        $codePagination="";
        return $codePagination;
    }

    /**
     * permet de rechercher le nombre d'enregistrement et d'en déduire
     * le nombre de pages en fonction de la limit fixée
     *
     * @return int
     */
    public function getPages(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entity sur laquelle nous devons paginer !
            Utilisez la méthode setEntityClass de votre objet PaginationService.");
        }

        $repo=$this->manager->getRepository($this->entityClass);
        $total=count($repo->findAll());
        $pages=ceil($total/$this->limit); // fonction php qui arrondi à l'entier supérieur
        return $pages;
    }

    /**
     * permet de rendre la vue twig de la pagination
     *
     * @return void
     */
    public function display(){
        $this->twig->display($this->getTemplatePath(),[
            'currentPage'   =>$this->getCurrentPage(),
            'pages'         => $this->getPages(),
            'route'         =>$this->getRoute()
        ]);
    }

    /**
     * Get the value of entityClass
     */ 
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */ 
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of limit
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of currentPage
     */ 
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */ 
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get the value of route
     */ 
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the value of route
     *
     * @return  self
     */ 
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get the value of templatePath
     */ 
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Set the value of templatePath
     *
     * @return  self
     */ 
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }
}