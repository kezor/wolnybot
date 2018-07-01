<?php

namespace App\Service;


use App\Building\Farmland;
use App\BuildingType;
use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Player;
use App\Repository\FarmlandRepository;
use App\Repository\FarmRepository;
use App\Repository\ProductRepository;
use App\Product;

class GameService
{
    /**
     * @var ConnectorInterface|WolniFarmerzyConnector
     */
    protected $connector;

    /**
     * @var Player
     */
    private $player;

    private $loggedIn = false;

    public function __construct(Player $player, ConnectorInterface $connector = null)
    {
        if (!$connector) {
            $connector = new WolniFarmerzyConnector();
        }

        $this->connector = $connector;
        $this->player = $player;
        $this->loggedIn = $this->connector->login($player);
    }

    /**
     * @return bool
     */
    public function isPlayerLoggedIn()
    {
        return $this->loggedIn;
    }

    public function updateStock()
    {
        $dashboardData = $this->connector->getDashboardData();

        $stocks = $dashboardData['updateblock']['stock']['stock'];

        $updatedItemsInStock = [];

        foreach ($stocks as $product) {
            foreach ($product as $level1) {
                foreach ($level1 as $level2) {
                    /** @var Product $product */
                    $product = ProductRepository::getStock($level2, $this->player);
                    $product->amount = $level2['amount'];
                    $product->save();
                    $updatedItemsInStock[] = $product->id;
                }
            }
        }

        $emptyItemsInStock = ProductRepository::getEmptyItems($updatedItemsInStock, $this->player);

        /** @var Product $item */
        foreach ($emptyItemsInStock as $item) {
            $item->amount = 0;
            $item->save();
        }
    }

    public function updateBuildings()
    {
        $dashboardData = $this->connector->getDashboardData();

        $farms = $dashboardData['updateblock']['farms']['farms'];

        foreach ($farms as $farmId => $farmData) {
            $farm = FarmRepository::getFarm($farmId, $this->player);
            foreach ($farmData as $spaceData) {
                if ($spaceData['status'] == 1) {
                    switch ($spaceData['buildingid']) {
                        case BuildingType::FARMLAND:
//                            var_dump('Updating....');
                            $farmland = FarmlandRepository::getFarmland($farm, $this->player, $spaceData);
//                            $farmland->fillInFields();
                            $this->updateFields($farmland);
                            break;
//                        case BuildingType::HOVEL:
//                            $this->processHovel($spaceData, $farmId);
//                            break;
                    }
                    $this->usedSeeds = []; // reset used products for new space}
                }


            }
            $farm->push();
        }
    }

    public function updateFields(Farmland $farmland)
    {
        $fieldsData = $this->connector->getFarmlandFields($farmland);
        $fields     = $fieldsData['datablock'][1];

        $updatedFieldIndexes = [];

        if ($fields != 0) {
            foreach ($fields as $key => $fieldData) {
                if (!is_numeric($key)) {
                    continue;
                }
                $farmland->updateField($fieldData);
                $updatedFieldIndexes[] = $fieldData['teil_nr'];
            }
        }
        $farmland->clearFields($updatedFieldIndexes);
    }
}