<?php

namespace Ceres\Contexts;

use IO\Services\ItemLoader\Extensions\TwigLoaderPresets;
use IO\Services\ItemLoader\Services\ItemLoaderService;

class ItemSearchContext extends GlobalContext implements ContextInterface
{
    public $page;
    public $isSearch;
    public $searchString;
    public $paginatedResult;
    
    public function init($params, $templateContainer)
    {
        parent::init($params, $templateContainer);
    
        /** @var TwigLoaderPresets $twigLoaderPresets */
        $twigLoaderPresets = pluginApp(TwigLoaderPresets::class);
        $presets = $twigLoaderPresets->getGlobals();
    
        /** @var ItemLoaderService $itemLoaderService */
        $itemLoaderService = pluginApp(ItemLoaderService::class);
        
        $this->page = $this->getParam('page', 1);
        $rowsPerPage = $this->ceresConfig->pagination->getRowsPerPage();
        $columnsPerPage = $this->ceresConfig->pagination->getColumnsPerPage();
        $itemsPerPage = $this->getParam('itemsPerPage', $rowsPerPage[0] * $columnsPerPage);
        $this->isSearch = $this->templateService->isSearch();
        
        if($this->isSearch)
        {
            $sorting = $this->ceresConfig->sorting->getDefaultSortingSearch();
            $this->searchString = $this->getParam('query', '');
        }
        else
        {
            $sorting = $this->ceresConfig->sorting->getDefaultSorting();
        }

        $requestSorting = $this->getParam('sorting', '');
        if(strlen($requestSorting))
        {
            $sorting = $requestSorting;
        }
        
        $options = [
            'query'             => $this->searchString,
            'page'              => $this->page,
            'items'             => $itemsPerPage,
            'sorting'           => $sorting,
        ];
    
        $this->paginatedResult = $itemLoaderService->loadForTemplate('Ceres::ItemList.ItemListView', $presets['itemLoaderPresets']['search'], $options);
    }
}