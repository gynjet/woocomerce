<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package WPSEO_Local\Main
 * @since   7.7
 */

namespace Yoast\WP\Local\Repositories;

if ( ! \class_exists( Business_Types_Repository::class ) ) {

	/**
	 * WPSEO_Local_Business_Types_Repository class. Handles all basic needs for the plugin, like custom post_type/taxonomy.
	 */
	class Business_Types_Repository {

		/**
		 * An array of business types.
		 *
		 * Initially null to make sure the assignment is done only once.
		 *
		 * @var array|null
		 */
		protected $business_types = null;

		/**
		 * A flattened array of all business types for usage in <select> boxes
		 *
		 * Initially null so we can prevent executing this multiple times
		 *
		 * @var array|null
		 */
		private $flattened_business_types = null;

		/**
		 * Check whether a given business type is a child of a given parent type.
		 *
		 * @param string $parent The given parent business type.
		 * @param string $child  The child business type on which to match.
		 * @param array  $array  The business types array or the child array when used recursively.
		 * @param array  $path   The path containing the route to the current item.
		 *
		 * @return bool True when the parent / child combination is found in the business types array.
		 */
		public function is_business_type_child_of( $parent, $child, $array = [], $path = [] ) {
			if ( $parent === $child ) {
				return true;
			}

			if ( empty( $array ) ) {
				$this->populate_business_types();
				$array = $this->business_types;
			}

			foreach ( $array as $key => $value ) {
				if ( \in_array( $parent, $path, true ) ) {
					if ( isset( $value['children'] ) && \is_array( $value['children'] ) && \array_key_exists( $child, $value['children'] ) ) {
						return true;
					}

					if ( \array_key_exists( $child, $array ) ) {
						return true;
					}
				}

				if ( isset( $value['children'] ) && ! empty( $value['children'] ) ) {
					$path[] = $key;
					$result = $this->is_business_type_child_of( $parent, $child, $value['children'], $path );
					if ( $result ) {
						return $result;
					}

					unset( $path[ \array_pop( $path ) ] );
				}
			}

			return false;
		}

		/**
		 * Return a flat array of all Business Type options.
		 *
		 * @return array A flat array of all Business Types.
		 */
		public function get_business_types() {
			if ( $this->flattened_business_types === null ) {
				$this->populate_business_types();
				$this->flattened_business_types = [];
				$this->flatten_business_types_array( $this->business_types );
			}

			return $this->flattened_business_types;
		}

		/**
		 * Function to recursively go through the business types array and flatten it.
		 *
		 * The result is assigned to the $flattened_business_types property.
		 *
		 * @param array $array Business types array to flatten.
		 * @param int   $level Nesting level of the current iteration.
		 */
		private function flatten_business_types_array( array $array, $level = 0 ) {

			foreach ( $array as $key => $value ) {
				// If $value is an array.
				if ( \is_array( $value ) ) {
					// We need to loop through it.
					if ( isset( $value['label'] ) ) {
						$this->flattened_business_types[ $key ] = \str_repeat( '&mdash; ', $level ) . $value['label'];
					}
					if ( ! empty( $value['children'] ) && \is_array( $value['children'] ) ) {
						$this->flatten_business_types_array( $value['children'], ( $level + 1 ) );
					}

					continue;
				}

				// It is not an array, so print it out.
				$this->flattened_business_types[ $key ] = \str_repeat( '&mdash; ', $level ) . $value;
			}
		}

		/**
		 * Populate the business types and apply filters.
		 *
		 * Returns early in case the business types have been set.
		 */
		protected function populate_business_types() {
			if ( $this->business_types !== null ) {
				return;
			}

			$this->business_types = [
				'Organization' => [
					/* translators: This should be translated according to the definition on Schema.org. For more information, visit: https://schema.org/Organization */
					'label'    => \__( 'Organization', 'yoast-local-seo' ),
					'children' => [
						/* translators: This should be translated according to the definition on Schema.org. For more information, visit: https://schema.org/Airline */
						'Airline'                 => \__( 'Airline', 'yoast-local-seo' ),
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Consortium */
						'Consortium'              => \__( 'Consortium', 'yoast-local-seo' ),
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Corporation */
						'Corporation'             => \__( 'Corporation', 'yoast-local-seo' ),
						'EducationalOrganization' => [
							/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/EducationalOrganization */
							'label'    => \__( 'Educational organization', 'yoast-local-seo' ),
							'children' => [
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/CollegeOrUniversity */
								'CollegeOrUniversity' => \__( 'College or university', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ElementarySchool */
								'ElementarySchool'    => \__( 'Elementary school', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HighSchool */
								'HighSchool'          => \__( 'High school', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MiddleSchool */
								'MiddleSchool'        => \__( 'Middle school', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Preschool */
								'Preschool'           => \__( 'Preschool', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/School */
								'School'              => \__( 'School', 'yoast-local-seo' ),
							],
						],
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/FundingScheme */
						'FundingScheme'           => \__( 'Funding scheme', 'yoast-local-seo' ),
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/GovernmentOrganization */
						'GovernmentOrganization'  => \__( 'Government organization', 'yoast-local-seo' ),
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/LibrarySystem */
						'LibrarySystem'           => \__( 'Library system', 'yoast-local-seo' ),
						'LocalBusiness'           => [
							/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/LocalBusiness */
							'label'    => \__( 'Local business', 'yoast-local-seo' ),
							'children' => [
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AnimalShelter */
								'AnimalShelter'               => \__( 'Animal shelter', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ArchiveOrganization */
								'ArchiveOrganization'         => \__( 'Archive organization', 'yoast-local-seo' ),
								'AutomotiveBusiness'          => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutomotiveBusiness */
									'label'    => \__( 'Automotive business', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutoBodyShop */
										'AutoBodyShop'     => \__( 'Auto body shop', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutoDealer */
										'AutoDealer'       => \__( 'Auto dealer', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutoPartsStore */
										'AutoPartsStore'   => \__( 'Auto parts store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutoRental */
										'AutoRental'       => \__( 'Auto rental', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutoRepair */
										'AutoRepair'       => \__( 'Auto repair', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutoWash */
										'AutoWash'         => \__( 'Auto wash', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/GasStation */
										'GasStation'       => \__( 'Gas station', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MotorcycleDealer */
										'MotorcycleDealer' => \__( 'Motorcycle dealer', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MotorcycleRepair */
										'MotorcycleRepair' => \__( 'Motorcycle repair', 'yoast-local-seo' ),
									],
								],
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ChildCare */
								'ChildCare'                   => \__( 'Child care', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Dentist */
								'Dentist'                     => \__( 'Dentist', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/DryCleaningOrLaundry */
								'DryCleaningOrLaundry'        => \__( 'Dry cleaning or laundry', 'yoast-local-seo' ),
								'EmergencyService'            => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/EmergencyService */
									'label'    => \__( 'Emergency service', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/FireStation */
										'FireStation'   => \__( 'Fire station', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Hospital */
										'Hospital'      => \__( 'Hospital', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PoliceStation */
										'PoliceStation' => \__( 'Police station', 'yoast-local-seo' ),
									],
								],
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/EmploymentAgency */
								'EmploymentAgency'            => \__( 'Employment agency', 'yoast-local-seo' ),
								'EntertainmentBusiness'       => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/EntertainmentBusiness */
									'label'    => \__( 'Entertainment business', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AdultEntertainment */
										'AdultEntertainment' => \__( 'Adult entertainment', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AmusementPark */
										'AmusementPark'      => \__( 'Amusement park', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ArtGallery */
										'ArtGallery'         => \__( 'Art gallery', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Casino */
										'Casino'             => \__( 'Casino', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ComedyClub */
										'ComedyClub'         => \__( 'Comedy club', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MovieTheater */
										'MovieTheater'       => \__( 'Movie theater', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/NightClub */
										'NightClub'          => \__( 'Night club', 'yoast-local-seo' ),
									],
								],
								'FinancialService'            => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/FinancialService */
									'label'    => \__( 'Financial service', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AccountingService */
										'AccountingService' => \__( 'Accounting service', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutomatedTeller */
										'AutomatedTeller'   => \__( 'Automated teller', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/BankOrCreditUnion */
										'BankOrCreditUnion' => \__( 'Bank or credit union', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/InsuranceAgency */
										'InsuranceAgency'   => \__( 'Insurance agency', 'yoast-local-seo' ),
									],
								],
								'FoodEstablishment'           => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/FoodEstablishment */
									'label'    => \__( 'Food establishment', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Bakery */
										'Bakery'             => \__( 'Bakery', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/BarOrPub */
										'BarOrPub'           => \__( 'Bar or pub', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Brewery */
										'Brewery'            => \__( 'Brewery', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/CafeOrCoffeeShop */
										'CafeOrCoffeeShop'   => \__( 'Cafe or coffee shop', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Distillery */
										'Distillery'         => \__( 'Distillery', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/FastFoodRestaurant */
										'FastFoodRestaurant' => \__( 'Fast food restaurant', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/IceCreamShop */
										'IceCreamShop'       => \__( 'Ice cream shop', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Restaurant */
										'Restaurant'         => \__( 'Restaurant', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Winery */
										'Winery'             => \__( 'Winery', 'yoast-local-seo' ),
									],
								],
								'GovernmentOffice'            => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/GovernmentOffice */
									'label'    => \__( 'Government office', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PostOffice */
										'PostOffice' => \__( 'Post office', 'yoast-local-seo' ),
									],
								],
								'HealthAndBeautyBusiness'     => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HealthAndBeautyBusiness */
									'label'    => \__( 'Health and beauty business', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/BeautySalon */
										'BeautySalon'  => \__( 'Beauty salon', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/DaySpa */
										'DaySpa'       => \__( 'Day spa', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HairSalon */
										'HairSalon'    => \__( 'Hair salon', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HealthClub */
										'HealthClub'   => \__( 'Health club', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/NailSalon */
										'NailSalon'    => \__( 'Nail salon', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/TattooParlor */
										'TattooParlor' => \__( 'Tattoo parlor', 'yoast-local-seo' ),
									],
								],
								'HomeAndConstructionBusiness' => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HomeAndConstructionBusiness */
									'label'    => \__( 'Home and construction business', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Electrician */
										'Electrician'       => \__( 'Electrician', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/GeneralContractor */
										'GeneralContractor' => \__( 'General contractor', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HVACBusiness */
										'HVACBusiness'      => \__( 'HVAC business', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HousePainter */
										'HousePainter'      => \__( 'House painter', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Locksmith */
										'Locksmith'         => \__( 'Locksmith', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MovingCompany */
										'MovingCompany'     => \__( 'Moving company', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Plumber */
										'Plumber'           => \__( 'Plumber', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/RoofingContractor */
										'RoofingContractor' => \__( 'Roofing contractor', 'yoast-local-seo' ),
									],
								],
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/InternetCafe */
								'InternetCafe'                => \__( 'Internet caf&eacute;', 'yoast-local-seo' ),
								'LegalService'                => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/LegalService */
									'label'    => \__( 'Legal service', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Attorney */
										'Attorney' => \__( 'Attorney', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Notary */
										'Notary'   => \__( 'Notary', 'yoast-local-seo' ),
									],
								],
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Library */
								'Library'                     => \__( 'Library', 'yoast-local-seo' ),
								'LodgingBusiness'             => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/LodgingBusiness */
									'label'    => \__( 'Lodging business', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/BedAndBreakfast */
										'BedAndBreakfast' => \__( 'Bed and breakfast', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Campground */
										'Campground'      => \__( 'Campground', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Hostel */
										'Hostel'          => \__( 'Hostel', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Hotel */
										'Hotel'           => \__( 'Hotel', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Motel */
										'Motel'           => \__( 'Motel', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Resort */
										'Resort'          => \__( 'Resort', 'yoast-local-seo' ),
									],
								],
								'MedicalBusiness'             => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MedicalBusiness */
									'label'    => \__( 'Medical business', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/CommunityHealth */
										'CommunityHealth' => \__( 'Community health', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Dentist */
										'Dentist'         => \__( 'Dentist', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Dermatology */
										'Dermatology'     => \__( 'Dermatology', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/DietNutrition */
										'DietNutrition'   => \__( 'Diet nutrition', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Emergency */
										'Emergency'       => \__( 'Emergency', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Geriatric */
										'Geriatric'       => \__( 'Geriatric', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Gynecologic */
										'Gynecologic'     => \__( 'Gynecologic', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MedicalClinic */
										'MedicalClinic'   => \__( 'Medical clinic', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Midwifery */
										'Midwifery'       => \__( 'Midwifery', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Nursing */
										'Nursing'         => \__( 'Nursing', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Obstetric */
										'Obstetric'       => \__( 'Obstetric', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Oncologic */
										'Oncologic'       => \__( 'Oncologic', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Optician */
										'Optician'        => \__( 'Optician', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Optometric */
										'Optometric'      => \__( 'Optometric', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Otolaryngologic */
										'Otolaryngologic' => \__( 'Otolaryngologic', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Pediatric */
										'Pediatric'       => \__( 'Pediatric', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Pharmacy */
										'Pharmacy'        => \__( 'Pharmacy', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Physician */
										'Physician'       => \__( 'Physician', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Physiotherapy */
										'Physiotherapy'   => \__( 'Physiotherapy', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PlasticSurgery */
										'PlasticSurgery'  => \__( 'Plastic surgery', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Podiatric */
										'Podiatric'       => \__( 'Podiatric', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PrimaryCare */
										'PrimaryCare'     => \__( 'Primary care', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Psychiatric */
										'Psychiatric'     => \__( 'Psychiatric', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PublicHealth */
										'PublicHealth'    => \__( 'Public health', 'yoast-local-seo' ),
									],
								],
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ProfessionalService */
								'ProfessionalService'         => \__( 'Professional service', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/RadioStation */
								'RadioStation'                => \__( 'Radio station', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/RealEstateAgent */
								'RealEstateAgent'             => \__( 'Real estate agent', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/RecyclingCenter */
								'RecyclingCenter'             => \__( 'Recycling center', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/SelfStorage */
								'SelfStorage'                 => \__( 'Self storage', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ShoppingCenter */
								'ShoppingCenter'              => \__( 'Shopping center', 'yoast-local-seo' ),
								'SportsActivityLocation'      => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/SportsActivityLocation */
									'label'    => \__( 'Sports activity location', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/BowlingAlley */
										'BowlingAlley'       => \__( 'Bowling alley', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ExerciseGym */
										'ExerciseGym'        => \__( 'Exercise gym', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/GolfCourse */
										'GolfCourse'         => \__( 'Golf course', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HealthClub */
										'HealthClub'         => \__( 'Health club', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PublicSwimmingPool */
										'PublicSwimmingPool' => \__( 'Public swimming pool', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/SkiResort */
										'SkiResort'          => \__( 'Ski resort', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/SportsClub */
										'SportsClub'         => \__( 'Sports club', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/StadiumOrArena */
										'StadiumOrArena'     => \__( 'Stadium or arena', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/TennisComplex */
										'TennisComplex'      => \__( 'Tennis complex', 'yoast-local-seo' ),
									],
								],
								'Store'                       => [
									/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Store */
									'label'    => \__( 'Store', 'yoast-local-seo' ),
									'children' => [
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/AutoPartsStore */
										'AutoPartsStore'       => \__( 'Auto parts store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/BikeStore */
										'BikeStore'            => \__( 'Bike store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/BookStore */
										'BookStore'            => \__( 'Book store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ClothingStore */
										'ClothingStore'        => \__( 'Clothing store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ComputerStore */
										'ComputerStore'        => \__( 'Computer store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ConvenienceStore */
										'ConvenienceStore'     => \__( 'Convenience store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/DepartmentStore */
										'DepartmentStore'      => \__( 'Department store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ElectronicsStore */
										'ElectronicsStore'     => \__( 'Electronics store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Florist */
										'Florist'              => \__( 'Florist', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/FurnitureStore */
										'FurnitureStore'       => \__( 'Furniture store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/GardenStore */
										'GardenStore'          => \__( 'Garden store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/GroceryStore */
										'GroceryStore'         => \__( 'Grocery store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HardwareStore */
										'HardwareStore'        => \__( 'Hardware store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HobbyShop */
										'HobbyShop'            => \__( 'Hobby shop', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/HomeGoodsStore */
										'HomeGoodsStore'       => \__( 'Home goods store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/JewelryStore */
										'JewelryStore'         => \__( 'Jewelry store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/LiquorStore */
										'LiquorStore'          => \__( 'Liquor store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MensClothingStore */
										'MensClothingStore'    => \__( 'Mens clothing store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MobilePhoneStore */
										'MobilePhoneStore'     => \__( 'Mobile phone store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MovieRentalStore */
										'MovieRentalStore'     => \__( 'Movie rental store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MusicStore */
										'MusicStore'           => \__( 'Music store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/OfficeEquipmentStore */
										'OfficeEquipmentStore' => \__( 'Office equipment store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/OutletStore */
										'OutletStore'          => \__( 'Outlet store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PawnShop */
										'PawnShop'             => \__( 'Pawn shop', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PetStore */
										'PetStore'             => \__( 'Pet store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ShoeStore */
										'ShoeStore'            => \__( 'Shoe store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/SportingGoodsStore */
										'SportingGoodsStore'   => \__( 'Sporting goods store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/TireShop */
										'TireShop'             => \__( 'Tire shop', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ToyStore */
										'ToyStore'             => \__( 'Toy store', 'yoast-local-seo' ),
										/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/WholesaleStore */
										'WholesaleStore'       => \__( 'Wholesale store', 'yoast-local-seo' ),
									],
								],
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/TelevisionStation */
								'TelevisionStation'           => \__( 'Television station', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/TouristInformationCenter */
								'TouristInformationCenter'    => \__( 'Tourist information center', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/TravelAgency */
								'TravelAgency'                => \__( 'Travel agency', 'yoast-local-seo' ),
							],
						],
						'MedicalOrganization'     => [
							/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MedicalOrganization */
							'label'    => \__( 'Medical organization', 'yoast-local-seo' ),
							'children' => [
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Dentist */
								'Dentist'        => \__( 'Dentist', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/DiagnosticLab */
								'DiagnosticLab'  => \__( 'Diagnostic lab', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Hospital */
								'Hospital'       => \__( 'Hospital', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MedicalClinic */
								'MedicalClinic'  => \__( 'Medical clinic', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Pharmacy */
								'Pharmacy'       => \__( 'Pharmacy', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Physician */
								'Physician'      => \__( 'Physician', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/VeterinaryCare */
								'VeterinaryCare' => \__( 'Veterinary care', 'yoast-local-seo' ),
							],
						],
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/NGO */
						'NGO'                     => \__( 'NGO', 'yoast-local-seo' ),
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/NewsMediaOrganization */
						'NewsMediaOrganization'   => \__( 'News media organization', 'yoast-local-seo' ),
						'PerformingGroup'         => [
							/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/PerformingGroup */
							'label'    => \__( 'Performing group', 'yoast-local-seo' ),
							'children' => [
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/DanceGroup */
								'DanceGroup'   => \__( 'Dance group', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/MusicGroup */
								'MusicGroup'   => \__( 'Music group', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/TheaterGroup */
								'TheaterGroup' => \__( 'Theater group', 'yoast-local-seo' ),
							],
						],
						'Project'                 => [
							/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/Project */
							'label'    => \__( 'Project', 'yoast-local-seo' ),
							'children' => [
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/FundingAgency */
								'FundingAgency'   => \__( 'Funding agency', 'yoast-local-seo' ),
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/ResearchProject */
								'ResearchProject' => \__( 'Research project', 'yoast-local-seo' ),
							],
						],
						'SportsOrganization'      => [
							/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/SportsOrganization */
							'label'    => \__( 'Sports organization', 'yoast-local-seo' ),
							'children' => [
								/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/SportsTeam */
								'SportsTeam' => \__( 'Sports team', 'yoast-local-seo' ),
							],
						],
						/* translators: This should be translated according to the definition on schema.org. For more information, visit: https://schema.org/WorkersUnion */
						'WorkersUnion'            => \__( 'Workers union', 'yoast-local-seo' ),
					],
				],
			];

			$this->business_types = apply_filters( 'yoast-local-seo-business-types', $this->business_types );
		}
	}
}
