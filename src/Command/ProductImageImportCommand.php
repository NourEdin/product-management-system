<?php
namespace App\Command;

use App\Entity\Image;
use App\Entity\Product;
use App\Service\PicsumAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as OI;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:image-import')]
class ProductImageImportCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private PicsumAPI $picsumAPI
    )
    {   
        parent::__construct();
    }
    public function configure() {
        $this->addOption("override", "o", InputOption::VALUE_OPTIONAL, "1 to override API errors, 0 to not. Default is 0", 0);
    }
    protected function execute(InputInterface $input, OI $output): int
    {
        $override = $input->getOption("override");

        $products = $this->em->getRepository(Product::class)->findAllExceptDeleted();

        foreach ($products as $product) {
            if ($product->getNumber() == null) {
                $output->writeln("Product {$product->getId()}-{$product->getName()} has no number. Skipping", OI::VERBOSITY_VERY_VERBOSE);
                continue;
            }
            $output->writeln("Adding image for product {$product->getId()}-{$product->getName()}", OI::VERBOSITY_VERBOSE);

            $output->writeln("Product number: {$product->getNumber()}", OI::VERBOSITY_VERBOSE);

            $output->writeln("Getting image info from API", OI::VERBOSITY_VERY_VERBOSE);

            //Extract the image id (digits) from the product number. Eg. P0123 --> 123
            $match = preg_match("/(\d+)/", $product->getNumber(), $matches);
            if (!$match) {
                $output->writeln("Couldn't extract image Id from product number");
                continue;
            }

            //Remove leading zeros
            $imageId = ltrim($matches[1], '0');

            $data = $this->picsumAPI->getImageDownloadUrl($imageId);

            if (isset($data['error'])) {
                $output->writeln($data['error']);
                $output->writeln(print_r($data, true), OI::VERBOSITY_VERY_VERBOSE);

                //If override is enabled, manually generate the image download_url 
                if ($override == 0) 
                    return Command::FAILURE;
                
                $url = "https://picsum.photos/id/$imageId/1000/1000";
                
            } else {
                $url = $data['url'];
            }

            $output->writeln("Fetched image url is: $url", OI::VERBOSITY_VERBOSE);

            //Replace product's image if it exists, to prevent flooding the database with orphan images
            $image = $product->getImage() ?: new Image();
            
            $image->setDownloadUrl($url);
            $product->setImage($image);

            $this->em->persist($image);
            $this->em->persist($product);

            $this->em->flush();

        }
        return Command::SUCCESS;

    }

}