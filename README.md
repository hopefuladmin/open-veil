# Open Veil

A WordPress plugin designed to structure, collect, and share experimental protocol data and community-submitted trials.

- [Description](#description)
- [Features](#features)
- [Installation](#installation)
- [Requirements](#requirements)
- [Configuration](#configuration)
  - [Plugin Settings](#plugin-settings)
- [REST API](#rest-api)
  - [Base URL](#base-url)
  - [Authentication](#authentication)
  - [Protocol Endpoints](#protocol-endpoints)
    - [Get All Protocols](#get-all-protocols)
    - [Get Protocol by ID](#get-protocol-by-id)
    - [Get Protocol by Slug](#get-protocol-by-slug)
    - [Get Protocol Trials](#get-protocol-trials)
    - [Get Protocols by Author](#get-protocols-by-author)
    - [Get Protocol CSL](#get-protocol-csl)
    - [Create Protocol](#create-protocol)
  - [Trial Endpoints](#trial-endpoints)
    - [Get All Trials](#get-all-trials)
    - [Get Trial by ID](#get-trial-by-id)
    - [Get Trial CSL](#get-trial-csl)
    - [Create Trial](#create-trial)
  - [Schema Endpoint](#schema-endpoint)
    - [Get Schema](#get-schema)
- [Postman Collection](#postman-collection)
- [Sample Data](#sample-data)
- [Templates](#templates)
- [Development](#development)
  - [Extending the Plugin](#extending-the-plugin)
  - [Building from Source](#building-from-source)
- [License](#license)
- [Community](#community)

## Description

Open Veil provides a structured framework for documenting experimental protocols and collecting trial data from community members. It's designed to facilitate scientific research by standardizing data collection and making it accessible through a user-friendly interface and comprehensive API.

## Features

- Custom post types for Protocols and Trials
- Taxonomies for categorizing experiments (Equipment, Laser Class, Substance, etc.)
- Advanced Custom Fields integration for structured data collection
- REST API for programmatic access to all data
- Customizable templates for displaying protocols and trials
- Support for both traditional and block themes
- Citation export in CSL-JSON format

## Installation

1. Upload the `open-veil` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings under 'Open Veil' in the admin menu

## Requirements

- WordPress 5.8 or higher
- PHP 8.0 or higher
- Advanced Custom Fields Pro (recommended but not required)

## Configuration

### Plugin Settings

Navigate to **Open Veil** in the WordPress admin menu to access the plugin settings:

- **Guest Submissions**: Allow non-logged-in users to submit trials
- **Claim Token Expiry**: Number of days before claim tokens expire
- **Email Notifications**: Enable/disable email notifications for trial submissions
- **API Access**: Control who can access the API (Public, Logged-in Users, Administrators)
- **Taxonomy Term Check**: Enable/disable checking and creating default taxonomy terms on each load

## REST API

Open Veil provides a comprehensive REST API for accessing and manipulating protocol and trial data.

### Base URL

All API endpoints are prefixed with:

`/wp-json/open-veil/v1/`

### Authentication

API access can be restricted based on the "API Access" setting in the plugin options:

- **Public**: No authentication required
- **Logged-in Users**: WordPress cookie authentication or Application Passwords
- **Administrators**: Admin-level authentication required

For endpoints that create or modify data, you'll need to include a WordPress nonce in the `X-WP-Nonce` header.

### Protocol Endpoints

#### Get All Protocols

```json
GET /protocol
```

Returns an array of all published protocols.

#### Get Protocol by ID

```json
GET /protocol/{id}
```

Returns a single protocol by its ID.

#### Get Protocol by Slug

```json
GET /protocol/name/{slug}
```

Returns a single protocol by its slug.

#### Get Protocol Trials

```json
GET /protocol/trials/{id}
```

Returns all trials associated with a specific protocol.

#### Get Protocols by Author

```json
GET /protocol/author/{author_id}
```

Returns all protocols created by a specific author.

#### Get Protocol CSL

```json
GET /protocol/{id}/csl
```

Returns citation data for a protocol in CSL-JSON format.

#### Create Protocol

```json
POST /protocol
```

Creates a new protocol. Required fields:

- `title`: Protocol title
- `meta`: Object containing protocol metadata
  - `laser_wavelength`: Laser wavelength in nanometers (400-700)
  - `laser_power`: Laser power in milliwatts (0-5)
  - `substance_dose`: Substance dose in grams
  - `projection_distance`: Projection distance in feet (1-20)

Optional fields:
- `content`: Protocol description/content
- `tax_input`: Object containing taxonomy terms to assign

Example request:

```json
{
  "title": "New Protocol Test",
  "content": "This is a test protocol created via the API.",
  "meta": {
    "laser_wavelength": 650,
    "laser_power": 1.0,
    "substance_dose": 1.0,
    "projection_distance": 5
  },
  "tax_input": {
    "laser_class": ["Class 2"],
    "diffraction_grating_spec": ["Standard"],
    "equipment": ["Laser", "Tripod"],
    "substance": ["N,N-DMT"],
    "administration_method": ["Inhalation"],
    "administration_protocol": ["Two inhales"],
    "projection_surface": ["Flat non-reflective"]
  }
}
```

### Trial Endpoints

#### Get All Trials

```json
GET /trial
```

Returns an array of all published trials.

#### Get Trial by ID

```json
GET /trial/{id}
```

Returns a single trial by its ID.

#### Get Trial CSL

```json
GET /trial/{id}/csl
```

Returns citation data for a trial in CSL-JSON format.

#### Create Trial

```json
POST /trial
```

Creates a new trial. Required fields:

- `title`: Trial title
- `meta`: Object containing trial metadata
  - `protocol_id`: ID of the protocol this trial is based on
  - `laser_wavelength`: Laser wavelength in nanometers (400-700)
  - `laser_power`: Laser power in milliwatts (0-5)
  - `substance_dose`: Substance dose in grams
  - `projection_distance`: Projection distance in feet (1-20)

Optional fields:
- `content`: Trial description/content
- `meta`: Additional metadata
  - `administration_notes`: Notes about the administration method
  - `additional_observers`: Whether there were additional observers (boolean)
- `tax_input`: Object containing taxonomy terms to assign

Example request:

```json
{
  "title": "Trial – 2025-04-01",
  "content": "Felt presence and geometric visuals after two inhales.",
  "meta": {
    "protocol_id": 1001,
    "laser_wavelength": 650,
    "laser_power": 0.8,
    "substance_dose": 1.1,
    "projection_distance": 6,
    "administration_notes": "Deep inhale using a quartz pipe.",
    "additional_observers": true
  },
  "tax_input": {
    "laser_class": ["Class 2"],
    "diffraction_grating_spec": ["Standard"],
    "equipment": ["Laser", "Tripod"],
    "substance": ["N,N-DMT"],
    "administration_method": ["Inhalation"],
    "administration_protocol": ["Two inhales"],
    "projection_surface": ["Flat non-reflective"]
  }
}
```

### Schema Endpoint

#### Get Schema

```json
GET /schema
```

Returns schema information including available taxonomies and metadata fields.

## Postman Collection

A Postman collection is included in the plugin to help you test and explore the API. The collection file is located at:

```
open-veil-postman-collection.json
```

To use the collection:

1. Import the file into Postman
2. Set up environment variables:
   - `base_url`: Your WordPress site URL
   - `wp_nonce`: A valid WordPress nonce for authenticated requests

## Sample Data

The plugin includes sample data to help you get started:

- `import.xml`: WordPress WXR file containing sample protocols and trials
- `sample.xml`: Extended sample data with more protocols and trials

To import the sample data:

1. Go to Tools > Import in your WordPress admin
2. Select "WordPress" and install the importer if prompted
3. Upload the `import.xml` or `sample.xml` file
4. Map the authors as desired and complete the import

## Templates

The plugin includes custom templates for displaying protocols and trials:

- `single-protocol.php`: Template for displaying a single protocol
- `archive-protocol.php`: Template for displaying protocol archives
- `single-trial.php`: Template for displaying a single trial
- `archive-trial.php`: Template for displaying trial archives

These templates can be overridden by creating files with the same names in your theme directory.

## Development

### Extending the Plugin

Open Veil is designed to be extensible. Here are some ways you can extend it:

- Add custom taxonomies by creating new classes in the `src/Taxonomy` directory
- Add custom fields by modifying the `src/ACF/Fields.php` file
- Add custom API endpoints by extending the `src/API/Rest.php` class

### Building from Source

If you want to modify the plugin, you'll need to set up a development environment:

1. Clone the repository
2. Install dependencies with Composer:
`composer install`
3. Make your changes
4. Build the plugin:
`composer build`

## License

This plugin is licensed under the GPL v3 or later.

## Community

Join our community to share your experiences, ask questions, and contribute!

- [Discord](https://discord.gg/codeofreality)
- [Code of Reality](https://codeofreality.com)
