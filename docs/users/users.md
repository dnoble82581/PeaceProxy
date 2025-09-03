# Users

## Overview

Users are the individuals who access and interact with the PeaceProxy application. They can be negotiators, supervisors, administrators, or other roles within an organization. Users are associated with a tenant (organization) and have specific permissions and access levels within the system.

## What is a User?

In PeaceProxy, a user represents an individual with access to the system. Users have:

- **Basic Information**: Name, email, password
- **Profile**: Avatar, contact details
- **Tenant Association**: Each user belongs to a specific tenant (organization)
- **Roles**: Users can have different roles in negotiations and conversations
- **Permissions**: Users have specific permissions that determine what actions they can perform

## Relationships

Users have relationships with several other entities in the system:

- **Tenant**: Each user belongs to a tenant (organization)
- **Negotiations**: Users can participate in multiple negotiations with different roles
- **Conversations**: Users can participate in multiple conversations
- **Messages**: Users can send messages in conversations
- **Whispers**: Users can send and receive private messages (whispers)
- **Warnings**: Users can create warnings about subjects
- **Triggers**: Users can create triggers for automated actions
- **Hooks**: Users can create hooks (points of connection with subjects)
- **Documents**: Users can have documents attached to their profile
- **Images**: Users can have images attached to their profile

## Creating a User

To create a new user:

1. Navigate to the Users section of the dashboard
2. Click the "New User" button
3. Fill in the required information:
   - Name
   - Email
   - Password
   - Role/permissions
4. Click "Create User"

Once created, the user will receive an email with instructions to set up their account.

## Updating a User

To update an existing user:

1. Navigate to the Users section of the dashboard
2. Select the user you want to update
3. Click the "Edit" button
4. Modify the user details as needed
5. Click "Save Changes"

You can update various aspects of a user:
- Basic information (name, email)
- Password
- Role/permissions
- Profile details
- Tenant association

## Deleting a User

To delete a user:

1. Navigate to the Users section of the dashboard
2. Select the user you want to delete
3. Click the "Delete" button
4. Confirm the deletion

**Note**: Deleting a user will not remove their associated data, such as messages they've sent or negotiations they've participated in. Instead, the user will be marked as inactive and will no longer be able to access the system.

## User Roles and Permissions

Users can have different roles in the system, which determine their permissions and access levels:

- **Administrator**: Full access to all features and settings
- **Supervisor**: Can manage negotiations, users, and view reports
- **Negotiator**: Can participate in negotiations and access related features
- **Observer**: Can view negotiations but cannot make changes

Within negotiations, users can have specific roles:
- **Lead Negotiator**: Primary point of contact with the subject
- **Secondary Negotiator**: Supports the lead negotiator
- **Intelligence Officer**: Gathers and provides information
- **Tactical Advisor**: Provides tactical advice
- **Mental Health Consultant**: Provides mental health expertise
- **Observer**: Observes the negotiation but does not actively participate

## Integration with Other Components

Users integrate with several other components of the PeaceProxy application:

- **Negotiations**: Users participate in negotiations with specific roles
- **Subjects**: Users interact with subjects through negotiations
- **Assessments**: Users can create and update risk assessments
- **Settings**: User settings affect how the application appears and functions for each user

## Best Practices

- Assign appropriate roles and permissions based on user responsibilities
- Regularly review and update user permissions
- Require strong passwords and enable two-factor authentication when possible
- Train users on proper use of the system before granting access
- Regularly audit user activity to ensure compliance with policies