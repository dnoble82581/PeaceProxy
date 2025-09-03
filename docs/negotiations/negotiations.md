# Negotiations

## Overview

Negotiations are a core component of the PeaceProxy application, representing structured communication processes with subjects in crisis situations. Negotiations are used to manage and track interactions with subjects, coordinate team efforts, and document the negotiation process.

## What is a Negotiation?

In PeaceProxy, a negotiation represents a structured communication process with one or more subjects. Negotiations have:

- **Status**: Tracks the current state of the negotiation (e.g., active, resolved, suspended)
- **Type**: Categorizes the negotiation (e.g., hostage situation, barricaded subject, suicide threat)
- **Timeline**: Tracks when the negotiation started and ended
- **Tags**: Custom labels for organizing and filtering negotiations
- **Participants**: Users involved in the negotiation with different roles
- **Subjects**: The individuals being negotiated with, who may have different roles in the negotiation
- **Related entities**: Hostages, conversations, hooks, documents, and risk assessments

## Relationships

Negotiations have relationships with several other entities in the system:

- **Tenant**: Each negotiation belongs to a tenant (organization)
- **Users**: Multiple users can be involved in a negotiation with different roles
- **Subjects**: Multiple subjects can be involved in a negotiation with different roles
- **Hostages**: Individuals being held against their will in a hostage situation
- **Conversations**: Communication threads within the negotiation
- **Hooks**: Key points of connection or leverage with the subject
- **Documents**: Files attached to the negotiation
- **Risk Assessments**: Evaluations of risk factors related to the negotiation

## Creating a Negotiation

To create a new negotiation:

1. Navigate to the Negotiations section of the dashboard
2. Click the "New Negotiation" button
3. Fill in the required information:
   - Title
   - Type
   - Primary subject (optional)
   - Additional details
4. Click "Create Negotiation"

Once created, you can add users, subjects, and other related entities to the negotiation.

## Updating a Negotiation

To update an existing negotiation:

1. Navigate to the Negotiations section of the dashboard
2. Select the negotiation you want to update
3. Click the "Edit" button
4. Modify the negotiation details as needed
5. Click "Save Changes"

You can update various aspects of a negotiation:
- Basic information (title, type, status)
- Add or remove users
- Add or remove subjects
- Add or update related entities (hostages, hooks, etc.)
- Attach documents

## Deleting a Negotiation

To delete a negotiation:

1. Navigate to the Negotiations section of the dashboard
2. Select the negotiation you want to delete
3. Click the "Delete" button
4. Confirm the deletion

**Note**: Deleting a negotiation will remove all associated data, including conversations, hooks, and other related entities. This action cannot be undone.

## Integration with Other Components

Negotiations integrate with several other components of the PeaceProxy application:

- **Users**: Users can be assigned different roles in a negotiation, such as lead negotiator, secondary negotiator, or observer
- **Subjects**: Subjects are the individuals being negotiated with and can have different roles in the negotiation
- **Assessments**: Risk assessments can be conducted for subjects involved in a negotiation
- **Settings**: Tenant settings can affect how negotiations are displayed and managed

## Best Practices

- Always assign at least one lead negotiator to a negotiation
- Regularly update the negotiation status to reflect the current situation
- Document important events and communications in the negotiation timeline
- Conduct risk assessments for subjects involved in high-risk negotiations
- Use tags to organize negotiations and make them easier to find