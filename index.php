<?php

namespace wrdickson\apibook;

require 'config/config.php';
require 'lib/DataConnector.php';
require 'lib/Reservations.php';
require 'lib/Reservation.php';
require 'lib/Folios.php';
require 'lib/Folio.php';
require 'lib/Customer.php';
require 'lib/Customers.php';
require 'lib/Account.php';
require 'lib/F3Auth.php';
require 'lib/RootSpaces.php';
require 'lib/RootSpace.php';
require 'lib/SpaceTypes.php';
require 'lib/TaxType.php';
require 'lib/TaxTypes.php';
require 'lib/SaleTypes.php';
require 'lib/SaleTypeGroups.php';

require 'vendor/autoload.php';

$f3 = \Base::instance();

$f3->route('GET /hello',
    function( $f3 ) {
      print json_encode( $f3['REQUEST'] );
    }
);

//  handles auth: login, test token, etc
require 'route_fragments/auth.php';

//  handles RootSpaces & Root Space
require 'route_fragments/root_spaces.php';

//  handles Reservations
require 'route_fragments/reservations.php';

//  handles Customers
require 'route_fragments/customers.php';

//  handles SpaceTypes
require 'route_fragments/space_types.php';

//  handles TaxTypes
require 'route_fragments/tax_types.php';

// handles SaleTypes
require 'route_fragments/sale_types.php';

// handles SaleTypeGroups
require 'route_fragments/sale_type_groups.php';

$f3->run();
