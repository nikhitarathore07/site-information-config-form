<?php

namespace Drupal\config_custom_form\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;
class CustomJsonPageApi implements ContainerInjectionInterface{

 /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $configFactory;

 public function __construct(ConfigFactoryInterface $config_factory) {
 	$this->configFactory = $config_factory;

 }

 /**
   * {@inheritdoc}
   */
 public static function create(ContainerInterface $container) {
    return new static( 
    	$container->get('config.factory')
          );
 }

 public function renderApi($api_key, NodeInterface $node) {

 	$config = $this->configFactory->getEditable('system.site');//
  if ((strtolower($config->get('siteapikey')) != $api_key) || ($node->bundle() != 'page')) {
    throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
  }
 	//get api key from the config
  $site_url = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(); 
  $contents = (array) json_decode(file_get_contents( $site_url.'/jsonapi/node/'.$node->bundle().'?filter=nid&filter[nid][value]=' . $node->id()));
return new JsonResponse($contents); 
 	// check the node and see if its of type page
 }
} 