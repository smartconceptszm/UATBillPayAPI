<?php 

//Tables with Changes


client_menus:
                  servicePoint - Delete

                  enquiryHandler - change to billingClient  
                  update the 'billingCleint' values in the DB Table


                  servicePointPrompt - change to customerAccountPrompt

complaints:
                  accountNumber - change to customerAccount

customerFieldUpdates:
                  accountNumber - change to customerAccount

messages:
                  accountNumber - change to customerAccount
                  client_id - change to channel_id
                  user_id - Change from bigint to varchar (36)

payments:
                  client_id - Delete
                  accountNumber - change to customerAccount

                  UPDATE `payments` 
                  SET `customerAccount` = `meterNumber`
                  WHERE `customerAccount` IS NULL AND `meterNumber` IS NOT NULL;

                  meterNumber - delete

SESSIONS:
                  accountNumber - change to customerAccount

                  UPDATE `sessions` 
                  SET `customerAccount` = `meterNumber`
                  WHERE `customerAccount` IS NULL AND `meterNumber` IS NOT NULL;
                  
                  meterNumber - delete


ServiceApplication:
                  accountNumber - change to customerAccount

surveyEntries:
                  accountNumber - change to customerAccount



INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151101', 'Residential  property rates', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151102', 'Commercial  property rates', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151103', 'Industrial property rates', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151104', 'Hospitality property rates', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151105', 'Mining  property rates', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151106', 'Power transmission property rates', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151107', 'Plant and Machinery property rates', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '151201', 'Personal levy ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152001', 'Consent fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152002', 'Survey fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152003', 'Building inspection-fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152004', 'Plan scrutiny fee', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152005', 'Change of premise use', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152006', 'Placement of Container ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152007', 'Rentals/lease of Councils properties', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152008', 'Non-Land Application forms fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152009', 'Rentals from houses', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152010', 'Sketch/ Site plan ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152011', 'Search fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152012', 'Notice board advert fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152013', 'Market fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152014', 'Parking fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152015', 'Carbon Trading fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152016', 'Loading fees (buses, trucks, trains, taxies etc.) ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152017', 'Affidavit fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152018', 'Hire of furnisher and office equipment ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152019', 'Proceeds from sale of fish products (Fingerings)', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152020', 'Hire of halls ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152021', 'Hire of grounds/stadia ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152022', 'Hoarding fees (Barricading)', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152023', 'Swimming pool fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152024', 'Recommendations fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152025', 'Grave reservation', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152026', 'Body remains (exhumation) fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152027', 'Body remains (inspections)fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152028', 'Boundary location (tombstone) fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152029', 'Wild and Domestic fruits/produce', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152030', 'Education fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152031', 'Motorbike and Bicycle fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152032', 'Hire of plant and equipment', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152033', 'Refuse disposal ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152034', 'Craft registration fee', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152035', 'Commercial & non-commercial Exhibitions', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152036', 'Hides and Skin movement ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152037', 'Farm visits ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152038', 'Library membership fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152039', 'Franchise fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152040', 'Storage fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152041', 'Dumb site fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152042', 'Animal Ante mortem fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152043', 'Registration (Brand marks)', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152044', 'Rentals from parks', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152045', 'Marriage fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152046', 'Meat inspection fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152047', 'Registration of clubs and societies ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152048', 'Slaughter inspections ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152049', 'Animal Pregnancy Test ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152050', 'Vendor Fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152051', 'Farm produce Fee', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152052', 'Flower vendor Fee', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152053', 'Certification of documents ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152054', 'Animal Post-mortems', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152055', 'Illegal Parking of vehicles', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152056', 'Repairs of cars/garage/car wash', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152057', 'Vehicles Towing Fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152058', 'Carcases movement ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152059', 'Land Record ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152060', 'Playing Parks Entry Fee', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152061', 'Funeral Parlour Fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152062', 'Hire of tents', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152063', 'Billboards and banners', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152064', 'Hire of Transport and Equipment', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152065', 'Council Minutes Extracts', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152066', 'Penalties', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152067', 'Ablution Fee ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152068', 'Truck Yard Fee', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152069', 'Laboratory charges', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152070', 'Bulk Transportation of Opaque Beer', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152071', 'Toll Fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152072', 'Booth fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152073', 'Ntemba fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152074', 'Sale of Bid Documents ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152075', 'Memorial Services Fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152076', 'Death Record Certificate', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152077', '152077 Club Membership ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152078', 'Erection of Tombstone', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152079', 'Aircraft landing fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152080', 'Pontoon services', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152081', 'Maritime and inland waterways fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152082', 'Telecommunication site rentals ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152083', 'Motor Vehicles examination fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152084', 'Consultation Clinic fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152085', 'Sale of fingerings', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152086', 'Abattoir (Slaughter Fees)', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152087', 'Animal Identification Mark Fees (Brand marks)', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152088', 'Farm call visits', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152089', 'Livestock Dipping and Spraying Fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152090', 'Health Insurance Services ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152091', 'Entry Fees, Tour guide and Sale of Crafts ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152092', 'Cultural Village Fees ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152093', 'Transit fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152094', 'Betting Fees', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '152199', 'Other fees and charges', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153001', 'Occupancy licence', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153002', 'Liquor licence ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153003', 'Firearm and ammunition licence ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153004', 'Petroleum Storage licence ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153005', 'Dog licence ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153006', 'Motor Vehicle Licences', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153007', 'Fish Licences/origin of fish', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '153008', 'Other Licences', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154001', 'Livestock Movement levy ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154002', 'Birds levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154003', 'Fish levy ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154004', 'Pole levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154005', 'Charcoal levy ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154006', 'Sand levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154007', 'Grave reservation levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154008', 'Quarry levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154009', 'Bicycle levy ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154010', 'Timber Levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154011', 'Telecommunication Mast Levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154012', 'Cane Levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154013', 'Stone Levy ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154014', 'Coal Levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154015', 'Hide/Exotic Levy ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154016', 'Grain Levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154017', ' 154017 Trading (Wholesale) Business Levy', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154018', 'Trading (Retail) Consumable groceries business', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154019', 'Retail Merchants non-consumable business ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154020', 'Trading (Retail) chain stores and supermarkets ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154021', 'Manufacturing ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154022', 'Agent Consumables', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154023', 'Agent non-Consumables ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154024', 'Trading (Retail) Automobiles ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154025', 'Theatre’s and cinematography', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154026', 'Hawker', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154027', 'Peddler', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154028', 'Filling Station', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154029', 'Professional Occupation ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154030', 'Scrap Metal Dealers', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154031', 'Car Wash ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154032', 'Hospitality', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154033', 'Commercial Banks ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154034', 'Micro- Finance and Money-Lenders', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '154099', 'Other levies', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155001', 'Health permits ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155002', 'Permit for opaque beer ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155003', 'Herbalist permit ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155004', 'Transportation of milk products', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155005', 'Transportation of meat products', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155006', 'Transportation of opaque beer ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155007', 'Nursery, pre-school permits', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155008', 'Burial permits and grave sites', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155009', 'Fire certificate ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155010', 'Extension of Business hours permits ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155011', 'Social gathering permit ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155012', 'Exploration permit ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155013', 'Primary, Secondary and Tertially permits ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');
INSERT INTO `billing_credentials` (`id`, `client_id`, `key`, `keyValue`, `description`, `created_at`, `updated_at`) VALUES
(uuid(), '39d62960-7303-11ee-b8ce-fec6e52a2330', '155099', 'Other Permits ', NULL, '2024-09-08 13:33:35', '2024-09-08 13:33:35');