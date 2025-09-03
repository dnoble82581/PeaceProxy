# Assessments

## Overview

Assessments in the PeaceProxy application are structured evaluations used to measure and document risk factors associated with subjects and negotiations. The primary type of assessment is the Risk Assessment, which helps negotiators identify potential dangers, develop appropriate strategies, and make informed decisions during negotiations.

## Assessment Templates

Assessment templates are reusable structures that define a set of questions for evaluating subjects or negotiations. Templates allow you to standardize your assessment process and ensure consistency across multiple assessments.

Key features of assessment templates:
- **Reusability**: Create once, use multiple times
- **Standardization**: Ensure all assessments follow the same structure
- **Efficiency**: Quickly create new assessments without recreating questions
- **Organization**: Group related questions by category and type

Each template consists of:
- **Name**: A descriptive name for the template
- **Description** (optional): Additional information about the template's purpose
- **Questions**: A set of questions with defined types and categories

## Creating Assessment Templates

To create a new assessment template:

1. Navigate to the Assessments section of the dashboard
2. Click the "Create Template" button
3. Fill in the required information:
   - Template name
   - Description (optional)
4. Click "Create Template"

Once created, you can add questions to your template.

## Adding Questions to Templates

To add questions to a template:

1. Navigate to the template you want to modify
2. Click the "Questions" button to view the template's questions
3. Click the "Add Question" button
4. Fill in the question details:
   - Question text
   - Question type (e.g., text, boolean, rating, select)
   - Question category (e.g., subject risk, tactical risk)
   - Required status (whether the question must be answered)
   - Options (for select, multiselect, radio, or checkbox questions)
5. Click "Add Question"

You can add as many questions as needed to thoroughly assess the subject.

## Using Templates to Create Assessments

To create an assessment using a template:

1. Navigate to the subject's assessments section
2. Click the "+" button to create a new assessment
3. Select the template you want to use from the dropdown
4. Click "Start Assessment"
5. Answer the questions from the template
6. Click "Submit Assessment" to complete the process

The assessment will be saved with your responses and can be reviewed later.

## What is a Risk Assessment?

In PeaceProxy, a risk assessment is a structured evaluation tool that consists of:

- **Title**: A descriptive name for the assessment
- **Subject**: The individual being assessed
- **Questions**: A set of questions organized by category and type
- **Responses**: Answers to the assessment questions
- **Tenant**: The organization that owns the assessment

Risk assessments help negotiators:
- Identify potential dangers and risk factors
- Develop appropriate negotiation strategies
- Document the decision-making process
- Track changes in risk levels over time

## Risk Assessment Questions

Risk assessment questions are the building blocks of assessments and have the following attributes:

- **Question Text**: The actual question being asked
- **Category**: The category the question belongs to (e.g., subject risk, tactical risk, operational risk)
- **Type**: The type of response expected (e.g., boolean, rating, text, select dropdown)
- **Required Status**: Whether the question must be answered
- **Options**: Possible answers for select, multiselect, radio, or checkbox questions
- **Risk Assessment**: The assessment the question belongs to

## Relationships

Risk assessments have relationships with several other entities in the system:

- **Tenant**: Each assessment belongs to a tenant (organization)
- **Subject**: The individual being assessed
- **Template**: The template used to create the assessment
- **Questions**: The set of questions that make up the assessment
- **Responses**: The answers provided to assessment questions
- **Users**: Users who create and respond to assessment questions

## Responding to Assessment Questions

To respond to assessment questions:

1. Navigate to the assessment you want to complete
2. For each question, provide an appropriate response based on the question type
3. Add any additional notes or context as needed
4. Click "Submit Assessment"

Responses can be updated as new information becomes available.

## Updating a Risk Assessment

To update an existing risk assessment:

1. Navigate to the Assessments section of the dashboard
2. Select the assessment you want to update
3. Click the "Edit" button
4. Modify the assessment details as needed
5. Click "Save Changes"

You can update various aspects of an assessment:
- Basic information (title)
- Associated subject
- Responses (update as new information becomes available)

## Deleting a Risk Assessment

To delete a risk assessment:

1. Navigate to the Assessments section of the dashboard
2. Select the assessment you want to delete
3. Click the "Delete" button
4. Confirm the deletion

**Note**: Deleting an assessment will remove all associated responses. This action cannot be undone.

## Question Categories

Risk assessment questions are organized into categories to help structure the assessment:

- **Subject Risk**: Questions about the subject's background, behavior, and risk factors
- **Tactical Risk**: Questions about tactical aspects of the situation and immediate response options
- **Operational Risk**: Questions about operational capabilities, resources, and constraints
- **Strategic Risk**: Questions about long-term implications, policy considerations, and strategic objectives
- **Environmental Risk**: Questions about physical environment, location, and environmental hazards
- **Team Risk**: Questions about team composition, capabilities, and potential vulnerabilities
- **Communication Risk**: Questions about communication channels, barriers, and strategies
- **General**: Other relevant questions that don't fit into specific categories

## Question Response Types

Questions can have different response types:

- **Text**: Simple single-line text input for short responses
- **Number**: Numeric input for quantitative data
- **Boolean**: Yes/No or True/False responses
- **Rating**: Numeric scale (e.g., 1-5) to indicate severity or likelihood
- **Text Area**: Multi-line text input for longer, detailed responses
- **Select Dropdown**: Selection of a single option from a dropdown list
- **Multi-Select**: Selection of multiple options from a list
- **Checkbox**: Toggle selection for one or more options
- **Radio Button**: Selection of a single option from visible choices
- **Date**: Calendar date selection
- **Time**: Time selection
- **Date & Time**: Combined date and time selection
- **File Upload**: Attachment of documents or other files

## Integration with Other Components

Assessments integrate with several other components of the PeaceProxy application:

- **Negotiations**: Assessments can be associated with specific negotiations
- **Subjects**: Assessments are typically conducted about specific subjects
- **Users**: Users create assessments, add questions, and provide responses

## Best Practices

- Create standardized assessment templates for common scenarios
- Regularly update assessments as new information becomes available
- Use appropriate question categories to organize the assessment
- Include a mix of question types to gather comprehensive information
- Document the reasoning behind responses, especially for high-risk factors
- Review completed assessments with the negotiation team to ensure shared understanding
- Use assessment results to inform negotiation strategies and decision-making