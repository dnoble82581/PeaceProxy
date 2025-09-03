# Settings

## Overview

Settings in the PeaceProxy application allow users to customize and configure various aspects of the system. There are two main types of settings: Agency (Tenant) Settings and User Settings. These settings control the appearance, behavior, and functionality of the application for organizations and individual users.

## Agency Settings

Agency Settings (also known as Tenant Settings) are configuration options that apply to the entire organization (tenant). These settings are typically managed by administrators and affect all users within the organization.

### Available Agency Settings

Agency settings include:

- **Organization Information**
  - Agency name
  - Agency logo
  - Contact information
  - Address
  - Timezone

- **Appearance Settings**
  - Theme colors
  - Default layout
  - Branding options

- **Notification Settings**
  - Email notification preferences
  - Alert thresholds
  - Notification recipients

- **Integration Settings**
  - Third-party service connections
  - API configurations
  - External system integrations

- **Security Settings**
  - Password policies
  - Session timeout duration
  - Two-factor authentication requirements

### Configuring Agency Settings

To configure Agency Settings:

1. Navigate to the Settings section of the dashboard
2. Select the "Agency Settings" tab
3. Modify the desired settings
4. Click "Save Agency Settings"

Only users with administrative privileges can modify Agency Settings.

## User Settings

User Settings are configuration options that apply to individual users. These settings allow users to personalize their experience within the constraints of the Agency Settings.

### Available User Settings

User settings include:

- **Profile Information**
  - Name
  - Email
  - Profile picture
  - Contact information
  - Password

- **Appearance Preferences**
  - Theme preference
  - Layout options
  - Font size

- **Notification Preferences**
  - Email notification frequency
  - In-app notification settings
  - Alert preferences

- **Accessibility Options**
  - High contrast mode
  - Screen reader compatibility
  - Keyboard shortcuts

### Configuring User Settings

To configure User Settings:

1. Navigate to the Settings section of the dashboard
2. Select the "Profile Settings" tab
3. Modify the desired settings
4. Click "Save Profile Settings"

Users can modify their own User Settings at any time.

## Impact on Application Behavior

Settings have various impacts on how the application behaves:

### Agency Settings Impact

- **Organization Information**: Affects branding and identification in reports and communications
- **Appearance Settings**: Determines the default look and feel of the application for all users
- **Notification Settings**: Controls when and how the organization receives alerts about important events
- **Integration Settings**: Enables or disables connections with external systems and services
- **Security Settings**: Enforces security policies across the organization

### User Settings Impact

- **Profile Information**: Affects how the user is identified in the system and communications
- **Appearance Preferences**: Personalizes the user interface for individual comfort and efficiency
- **Notification Preferences**: Determines what notifications the user receives and how they're delivered
- **Accessibility Options**: Makes the application more usable for users with specific needs

## Settings Storage

Agency Settings are stored in the `settings` JSON column of the `tenants` table in the database. This flexible structure allows for storing various configuration options without requiring database schema changes.

User Settings are partially stored in the user's record in the database and partially managed through browser cookies for appearance preferences.

## Best Practices

- **Agency Settings**
  - Review and update agency settings regularly to ensure they reflect current organizational needs
  - Document any significant changes to agency settings
  - Test the impact of setting changes before applying them in production
  - Limit access to agency settings to authorized administrators only

- **User Settings**
  - Encourage users to configure their settings for optimal productivity
  - Provide guidance on recommended settings for different user roles
  - Respect user preferences and avoid overriding them with global changes when possible
  - Ensure accessibility options are available and functioning properly

## Troubleshooting

If settings are not being applied correctly:

1. Verify that the settings were saved successfully
2. Clear browser cache and cookies
3. Log out and log back in to the application
4. Check for conflicts between agency settings and user settings
5. Ensure the user has the necessary permissions to apply the settings
6. Contact system administrator if the issue persists