CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "analysis_areas"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  "code" varchar not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "analysis_areas_name_unique" on "analysis_areas"("name");
CREATE UNIQUE INDEX "analysis_areas_code_unique" on "analysis_areas"("code");
CREATE TABLE IF NOT EXISTS "lab_matrices"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  "code" varchar not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "lab_matrices_name_unique" on "lab_matrices"("name");
CREATE UNIQUE INDEX "lab_matrices_code_unique" on "lab_matrices"("code");
CREATE TABLE IF NOT EXISTS "sample_containers"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  "description" text,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "sample_containers_name_unique" on "sample_containers"(
  "name"
);
CREATE TABLE IF NOT EXISTS "sample_preservers"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  "description" text,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "sample_preservers_name_unique" on "sample_preservers"(
  "name"
);
CREATE TABLE IF NOT EXISTS "methodologies"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "methodologies_name_unique" on "methodologies"("name");
CREATE TABLE IF NOT EXISTS "sampling_remarks"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "code" varchar not null,
  "description" text not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "sampling_remarks_code_unique" on "sampling_remarks"(
  "code"
);
CREATE TABLE IF NOT EXISTS "quote_remarks"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "code" varchar not null,
  "description" text not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "quote_remarks_code_unique" on "quote_remarks"("code");
CREATE TABLE IF NOT EXISTS "measurement_units"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "unit" varchar not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "label_colors"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "color" varchar not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "parameter_groups"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "name" varchar not null,
  "order" integer not null,
  "description" text,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "parameter_categories"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "name" varchar not null,
  "description" text,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "parameters"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  "price" numeric not null default '0',
  "unit_volume" varchar,
  "group_volume" varchar,
  "validity" varchar,
  "measurement_unit_id" integer,
  "lab_matrix_id" integer not null,
  "methodology_id" integer not null default '0',
  "sample_container_id" integer not null default '0',
  "label_color_id" integer,
  "parameter_group_id" integer,
  "parameter_category_id" integer,
  "sample_preserver_id" integer not null default '1',
  "sample_storage_id" integer not null,
  "analysis_area_id" integer not null,
  "quantification_low_range" integer,
  "quantification_mid_range" integer,
  "quantification_high_range" integer,
  "uncertainty_low_range" integer,
  "uncertainty_mid_range" integer,
  "uncertainty_high_range" integer,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id"),
  foreign key("measurement_unit_id") references "measurement_units"("id"),
  foreign key("lab_matrix_id") references "lab_matrices"("id"),
  foreign key("methodology_id") references "methodologies"("id"),
  foreign key("sample_container_id") references "sample_containers"("id"),
  foreign key("label_color_id") references "label_colors"("id"),
  foreign key("parameter_group_id") references "parameter_groups"("id"),
  foreign key("parameter_category_id") references "parameter_categories"("id"),
  foreign key("sample_preserver_id") references "sample_preservers"("id"),
  foreign key("sample_storage_id") references "sample_storages"("id"),
  foreign key("analysis_area_id") references "analysis_areas"("id")
);
CREATE TABLE IF NOT EXISTS "sample_storages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "identifier" varchar not null,
  "description" text,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "bundle_parameter"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "bundle_id" integer not null,
  "parameter_id" integer not null,
  foreign key("bundle_id") references "bundles"("id"),
  foreign key("parameter_id") references "parameters"("id")
);
CREATE TABLE IF NOT EXISTS "clients"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  "company" varchar not null,
  "industry_sector" varchar,
  "street" varchar not null,
  "external_number" varchar,
  "internal_number" varchar,
  "neighborhood" varchar not null,
  "zip_code" varchar not null,
  "city" varchar not null,
  "state" varchar not null,
  "website" varchar,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "client_contacts"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "is_main_contact" tinyint(1) not null default '0',
  "name" varchar not null,
  "phone" varchar,
  "cellphone" varchar,
  "email" varchar,
  "alt_email" varchar,
  "client_id" integer,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id"),
  foreign key("client_id") references "clients"("id")
);
CREATE TABLE IF NOT EXISTS "client_sampling_sites"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "version" integer not null default '1',
  "name" varchar not null,
  "is_main_site" tinyint(1) not null default '0',
  "industry_sector" varchar,
  "street" varchar not null,
  "external_number" varchar,
  "internal_number" varchar,
  "neighborhood" varchar,
  "city" varchar not null,
  "zip_code" varchar,
  "state" varchar not null,
  "contact_name" varchar not null,
  "contact_phone" varchar,
  "contact_cellphone" varchar,
  "contact_email" varchar,
  "contact_alt_email" varchar,
  "client_id" integer not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id"),
  foreign key("client_id") references "clients"("id")
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "alias" varchar not null
);
CREATE TABLE IF NOT EXISTS "regulation_parameter"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "parameter_id" integer not null,
  "regulation_id" integer not null,
  foreign key("parameter_id") references parameters("id") on delete no action on update no action,
  foreign key("regulation_id") references "regulations"("id") on delete cascade on update cascade
);
CREATE UNIQUE INDEX "regulation_parameter_unique" on "regulation_parameter"(
  "regulation_id",
  "parameter_id"
);
CREATE TABLE IF NOT EXISTS "regulatory_thresholds"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "min" varchar,
  "max" varchar,
  "parameter_id" integer not null,
  "regulation_id" integer not null,
  "regulation_instance_id" integer not null,
  foreign key("parameter_id") references parameters("id") on delete no action on update no action,
  foreign key("regulation_id") references "regulations"("id") on delete cascade on update cascade,
  foreign key("regulation_instance_id") references "regulation_instance_tree"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "regulations"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "lab_matrix_id" integer,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "observation" text not null default '',
  foreign key("deleted_by") references users("id") on delete no action on update no action,
  foreign key("updated_by") references users("id") on delete no action on update no action,
  foreign key("created_by") references users("id") on delete no action on update no action,
  foreign key("lab_matrix_id") references lab_matrices("id") on delete no action on update no action
);
CREATE TABLE IF NOT EXISTS "bundles"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "price" integer not null,
  "takes" integer not null,
  "regulation_id" integer not null,
  foreign key("regulation_id") references regulations("id") on delete cascade on update cascade,
  foreign key("deleted_by") references users("id") on delete no action on update no action,
  foreign key("updated_by") references users("id") on delete no action on update no action,
  foreign key("created_by") references users("id") on delete no action on update no action
);
CREATE TABLE IF NOT EXISTS "regulation_instance_tree"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "name" varchar not null,
  "parent_id" integer,
  "regulation_id" integer not null,
  "type" varchar check("type" in('node', 'definition')) not null,
  "alias" varchar not null default '',
  foreign key("regulation_id") references regulations("id") on delete cascade on update cascade,
  foreign key("parent_id") references regulation_instance_tree("id") on delete no action on update no action
);
CREATE TABLE IF NOT EXISTS "regulation_tree"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "name" varchar not null,
  "type" varchar,
  "nodable_id" integer,
  "parent_id" integer,
  "nodable_type" varchar check("nodable_type" in('App\Models\Regulatory\Structure\Regulation', 'App\Models\Regulatory\Structure\Bundle')),
  "alias" varchar not null default(''),
  foreign key("parent_id") references regulation_tree("id") on delete no action on update no action
);
CREATE UNIQUE INDEX "unique_node_index" on "regulation_tree"(
  "name",
  "parent_id",
  "alias"
);
CREATE TABLE IF NOT EXISTS "quotes"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "year" integer not null,
  "sequence_index" integer not null,
  "letter_index" integer not null,
  "gross_cost" integer not null,
  "price_adjustment" integer,
  "price_adjustment_percentage" integer,
  "subtotal" integer not null,
  "iva" integer not null,
  "net_cost" integer not null,
  "identifier" varchar not null,
  "objective" text not null,
  "notes" text,
  "sample_delivered_by_client" tinyint(1) not null default '0',
  "client_data_as_sampling_site" tinyint(1) not null default '0',
  "tree" text,
  "authorized" tinyint(1) not null default '0',
  "original_creator" integer not null,
  "parent_id" integer,
  "original_ancestor_id" integer,
  "validity" varchar not null default '30 días',
  "payment_method" varchar,
  "price_adjustment_notes" text,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id"),
  foreign key("original_creator") references "users"("id"),
  foreign key("parent_id") references "quotes"("id"),
  foreign key("original_ancestor_id") references "quotes"("id")
);
CREATE TABLE IF NOT EXISTS "quote_client_records"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "industry_sector" varchar,
  "address" varchar not null,
  "neighborhood" varchar not null,
  "city" varchar not null,
  "state" varchar not null,
  "zip_code" varchar not null,
  "quote_id" integer not null,
  "client_id" integer,
  foreign key("quote_id") references "quotes"("id")
);
CREATE TABLE IF NOT EXISTS "quote_selected_contacts"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "is_main_contact" tinyint(1) not null default '0',
  "phone" varchar,
  "cellphone" varchar,
  "email" varchar,
  "quote_id" integer not null,
  "client_contact_id" integer,
  foreign key("quote_id") references "quotes"("id"),
  foreign key("client_contact_id") references "client_contacts"("id")
);
CREATE TABLE IF NOT EXISTS "quote_selected_sampling_sites"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "industry_sector" varchar,
  "is_main_saite" tinyint(1) not null default '0',
  "address" varchar,
  "neighborhood" varchar,
  "city" varchar,
  "state" varchar,
  "zip_code" varchar,
  "phone" varchar,
  "contact_name" varchar,
  "quote_id" integer not null,
  "client_sampling_site_id" integer,
  foreign key("quote_id") references "quotes"("id"),
  foreign key("client_sampling_site_id") references "client_sampling_sites"("id")
);
CREATE TABLE IF NOT EXISTS "remarkables"(
  "id" integer primary key autoincrement not null,
  "remarkable_id" integer not null,
  "remarkable_type" varchar not null,
  "parameter_id" integer not null,
  foreign key("parameter_id") references "parameters"("id")
);
CREATE TABLE IF NOT EXISTS "quote_parameter_records"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "price" integer not null,
  "methodology" varchar not null,
  "parameter_id" integer not null,
  "quote_id" integer not null,
  foreign key("parameter_id") references "parameters"("id"),
  foreign key("quote_id") references "quotes"("id")
);
CREATE TABLE IF NOT EXISTS "quote_entries"(
  "id" integer primary key autoincrement not null,
  "entry_id" varchar not null,
  "title" varchar not null,
  "is_urgent" tinyint(1) not null,
  "form_factor" varchar not null,
  "objective" text not null,
  "concept" text not null,
  "bundle_price" integer not null default '0',
  "extras" integer not null default '0',
  "price_offset" integer not null default '0',
  "takes" integer not null default '1',
  "result_time_lapse" integer not null,
  "sample_type" varchar,
  "sample_reception_date" datetime,
  "sampling_date" datetime,
  "sample_temperature" integer,
  "sample_container_type" varchar,
  "total_containers" integer,
  "total_volume" varchar,
  "refrigerated" tinyint(1),
  "observation" text,
  "quote_id" integer not null,
  "matrix_id" integer not null,
  "quantity" integer not null default '1',
  "price_offset_notes" text,
  foreign key("quote_id") references "quotes"("id"),
  foreign key("matrix_id") references "lab_matrices"("id")
);
CREATE TABLE IF NOT EXISTS "quote_entry_parameters"(
  "id" integer primary key autoincrement not null,
  "quantity" integer not null,
  "expected_quantity" integer not null,
  "from_system" tinyint(1) not null,
  "from_main_report" tinyint(1) not null,
  "quote_id" integer not null,
  "quote_entry_id" integer not null,
  "parameter_id" integer not null,
  foreign key("quote_id") references "quotes"("id"),
  foreign key("quote_entry_id") references "quote_entries"("id"),
  foreign key("parameter_id") references "parameters"("id")
);
CREATE TABLE IF NOT EXISTS "quote_entry_reports"(
  "id" integer primary key autoincrement not null,
  "report_id" varchar not null,
  "structure_expanded_keys" text not null,
  "structure_selected_keys" text not null,
  "instance_expanded_keys" text not null,
  "instance_selected_keys" text not null,
  "is_main_report" tinyint(1) not null,
  "observation" text not null,
  "entry_id" integer not null,
  foreign key("entry_id") references "quote_entries"("id")
);
CREATE TABLE IF NOT EXISTS "report_thresholds"(
  "id" integer primary key autoincrement not null,
  "min" varchar,
  "max" varchar not null,
  "custom_boundary" varchar not null,
  "report_id" integer not null,
  "parameter_id" integer not null,
  foreign key("report_id") references "quote_entry_reports"("id"),
  foreign key("parameter_id") references "parameters"("id")
);
CREATE TABLE IF NOT EXISTS "quote_expenses"(
  "id" integer primary key autoincrement not null,
  "concept" varchar not null,
  "cost" integer not null,
  "quantity" integer not null,
  "quote_id" integer not null,
  foreign key("quote_id") references "quotes"("id")
);
CREATE TABLE IF NOT EXISTS "quote_notes"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "text" text not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "quote_commercial_terms"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer,
  "updated_by" integer,
  "deleted_by" integer,
  "deleted_at" datetime,
  "text" text not null,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  foreign key("deleted_by") references "users"("id")
);
CREATE UNIQUE INDEX "unique_parameter_remark" on "remarkables"(
  "parameter_id",
  "remarkable_id",
  "remarkable_type"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2024_11_12_161000_create_analysis_areas_table',1);
INSERT INTO migrations VALUES(5,'2024_11_14_183831_create_lab_matrices_table',1);
INSERT INTO migrations VALUES(6,'2024_11_20_231726_create_sample_containers_table',1);
INSERT INTO migrations VALUES(7,'2024_11_22_170641_create_sample_preservers_table',1);
INSERT INTO migrations VALUES(8,'2025_01_14_224040_create_methodologies_table',1);
INSERT INTO migrations VALUES(9,'2025_01_15_192115_create_sampling_remarks_table',1);
INSERT INTO migrations VALUES(10,'2025_01_16_175105_create_quote_remarks_table',1);
INSERT INTO migrations VALUES(11,'2025_01_22_085523_create_measurement_units_table',1);
INSERT INTO migrations VALUES(12,'2025_01_22_142106_create_label_colors_table',1);
INSERT INTO migrations VALUES(13,'2025_01_23_123910_create_parameter_groups_table',1);
INSERT INTO migrations VALUES(14,'2025_01_24_090701_create_parameter_categories_table',1);
INSERT INTO migrations VALUES(15,'2025_01_24_095224_create_parameters_table',1);
INSERT INTO migrations VALUES(16,'2025_01_27_111600_create_sample_storages_table',1);
INSERT INTO migrations VALUES(35,'2025_02_11_102046_create_tree_nodes_table',2);
INSERT INTO migrations VALUES(36,'2025_02_14_180940_create_standards_table',2);
INSERT INTO migrations VALUES(37,'2025_02_20_113930_create_standard_parameter_table',2);
INSERT INTO migrations VALUES(38,'2025_02_24_161044_create_subtree_nodes_table',2);
INSERT INTO migrations VALUES(39,'2025_02_26_180952_create_allowable_limits_table',2);
INSERT INTO migrations VALUES(40,'2025_03_04_090727_create_bundles_table',3);
INSERT INTO migrations VALUES(41,'2025_03_04_093813_create_bundle_parameter_table',3);
INSERT INTO migrations VALUES(86,'2025_03_14_110038_create_clients_table',4);
INSERT INTO migrations VALUES(89,'2025_03_14_121459_create_client_contacts_table',5);
INSERT INTO migrations VALUES(96,'2025_03_24_135246_create_client_sampling_sites_table',6);
INSERT INTO migrations VALUES(99,'2025_05_12_160023_rename_client_allowable_limits_table',7);
INSERT INTO migrations VALUES(100,'2025_05_13_083555_modifying_users_table',8);
INSERT INTO migrations VALUES(101,'2025_05_13_094005_set_alter_users_alias_table',9);
INSERT INTO migrations VALUES(106,'2025_05_19_120238_reimplement_standards_into_regulations',10);
INSERT INTO migrations VALUES(107,'2025_05_23_105139_add_alias_field_to_regulation_tree_table',11);
INSERT INTO migrations VALUES(108,'2025_05_28_103101_remove_name_column_from_regulation_tree_constructs',12);
INSERT INTO migrations VALUES(109,'2025_06_04_115020_add_type_to_regulation_instance_tree_table',13);
INSERT INTO migrations VALUES(110,'2025_06_04_160125_drop_is_leaf_from_regulation_instance_tree_table',14);
INSERT INTO migrations VALUES(111,'2025_06_09_175557_update_with_new_regulatory_structure_regulation_tree_table',15);
INSERT INTO migrations VALUES(112,'2025_06_12_083706_add_alias_column_to_regulation_instance_tree_table',16);
INSERT INTO migrations VALUES(113,'2025_07_21_113915_add_range_values_to_parameters_table',17);
INSERT INTO migrations VALUES(114,'2025_07_21_122940_add_observation_field_to_regulations_table',18);
INSERT INTO migrations VALUES(702,'2025_08_12_153437_change_company_to_name_in_client_sampling_sites_table',19);
INSERT INTO migrations VALUES(780,'2025_08_27_165647_create_quotes_table',20);
INSERT INTO migrations VALUES(781,'2025_08_27_172443_create_quote_client_records_table',20);
INSERT INTO migrations VALUES(782,'2025_08_27_174725_create_quote_selected_contacts_table',20);
INSERT INTO migrations VALUES(783,'2025_08_28_082559_create_quote_selected_sampling_sites_table',20);
INSERT INTO migrations VALUES(784,'2025_09_03_133012_create_parameter_observation_table',20);
INSERT INTO migrations VALUES(785,'2025_09_03_153101_create_quote_parameter_records_table',20);
INSERT INTO migrations VALUES(786,'2025_09_03_175222_create_quote_entries_table',20);
INSERT INTO migrations VALUES(787,'2025_09_05_120931_create_quote_entry_parameters_table',20);
INSERT INTO migrations VALUES(788,'2025_09_05_122639_create_quote_entry_reports_table',20);
INSERT INTO migrations VALUES(789,'2025_09_05_125912_create_report_thresholds_table',20);
INSERT INTO migrations VALUES(790,'2025_09_10_082317_create_quote_expenses_table',20);
INSERT INTO migrations VALUES(795,'2025_10_02_112424_create_quote_notes_table',21);
INSERT INTO migrations VALUES(796,'2025_10_02_133923_create_quote_commercial_terms_table',21);
INSERT INTO migrations VALUES(807,'2025_10_07_090727_rename_parameter_obsertvations_table',22);
INSERT INTO migrations VALUES(810,'2025_10_08_133439_alter_quotes_table',23);
INSERT INTO migrations VALUES(812,'2025_10_08_160020_alter_quote_entries_table',24);
INSERT INTO migrations VALUES(813,'2025_10_10_182528_remove_tree_leaves_from_quotes_table',25);
