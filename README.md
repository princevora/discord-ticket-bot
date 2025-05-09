# Discord Ticket Bot Commands

This document outlines the commands available for the Discord Ticket Bot, designed to manage support tickets within a Discord server. All commands are slash commands (prefixed with `/`) and are restricted to moderators (users with `Manage Channels` permission or a designated moderator role).

## Table of Contents
- [Command List](#command-list)
  - [/add :user](#add-user)
  - [/remove :user](#remove-user)
  - [/close](#close)
  - [/setup](#setup)
  - [/new :user](#new-user)
- [Permissions Overview](#permissions-overview)
- [Notes](#notes)

## Command List

### /add :user
**Description**: Adds a specified user to the current ticket channel, granting them access to view and participate in the ticket.

**Usage**: `/add :user`

**Parameters**:
- `:user` (required): The user to add to the ticket (mention or ID).

**Example**:
```
/add @JohnDoe
```
- Adds `@JohnDoe` to the ticket, allowing them to see and send messages in the ticket channel.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- The user must be a member of the server.
- Only works in an active ticket channel.
- The bot must have permission to modify channel permissions.

---

### /remove :user
**Description**: Removes a specified user from the current ticket channel, revoking their access to the ticket.

**Usage**: `/remove :user`

**Parameters**:
- `:user` (required): The user to remove from the ticket (mention or ID).

**Example**:
```
/remove @JohnDoe
```
- Removes `@JohnDoe` from the ticket, preventing them from viewing or sending messages in the ticket channel.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- The user must already be added to the ticket.
- Cannot remove the ticket creator or the bot itself.
- The bot must have permission to modify channel permissions.

---

### /close
**Description**: Closes the current ticket, locking the channel and optionally archiving or deleting it based on bot configuration.

**Usage**: `/close`

**Parameters**: None

**Example**:
```
/close
```
- Closes the ticket channel, preventing further messages and marking it as resolved.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- May generate a ticket transcript (if enabled) and send it to a configured log channel.
- Closed tickets may be archived (made read-only) or deleted, depending on bot settings.
- The bot requires `Manage Channels` and `Send Messages` permissions in the log channel (if applicable).

---

### /setup
**Description**: Configures the ticket system for the server, creating a ticket creation channel or panel where users can open tickets.

**Usage**: `/setup`

**Parameters**: None

**Example**:
```
/setup
```
- Creates a ticket creation channel (e.g., `#create-ticket`) with a message or button for users to open tickets.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role. (Note: In some cases, `Administrator` permission may be required for initial setup, depending on bot configuration.)

**Notes**:
- May prompt for additional configuration (e.g., category for ticket channels, support roles) via interactive messages.
- Only one ticket system can be set up per server unless otherwise configured.
- The bot requires `Manage Channels`, `Send Messages`, and `Embed Links` permissions to create the panel.
- Ensure the bot has access to the target category for ticket channels.

---

### /new :user
**Description**: Creates a new ticket for the specified user, opening a private channel for support or discussion.

**Usage**: `/new :user`

**Parameters**:
- `:user` (required): The user for whom the ticket is created (mention or ID).

**Example**:
```
/new @JohnDoe
```
- Creates a new ticket channel accessible to `@JohnDoe`, the moderator issuing the command, and other moderators.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- The ticket channel is typically created under a configured category.
- The bot may notify moderators (e.g., via mentions or a log channel).
- Regular users can create their own tickets via the `/setup`-generated panel, but this command is for moderators to initiate tickets on behalf of users.
- The bot requires `Manage Channels` and `Manage Permissions` to create and configure the ticket channel.

---

## Permissions Overview
- **Moderators**:
  - All commands (`/add`, `/remove`, `/close`, `/setup`, `/new`) are restricted to users with `Manage Channels` permission or a designated moderator role.
  - In some cases, `/setup` may require `Administrator` permission, depending on the bot’s configuration.
- **Regular Users**:
  - Cannot use any of the listed commands.
  - Can create tickets via the ticket panel or message set up with `/setup` (e.g., by clicking a button or reacting to a message).
- **Bot**:
  - Requires `Manage Channels`, `Send Messages`, `Embed Links`, and `Manage Permissions` for most operations.
  - Additional permissions like `View Channels` and `Read Message History` may be needed for full functionality.

Ensure roles and permissions are configured correctly in the Discord server to restrict commands to moderators and allow the bot to function properly.

---

## Notes
»

System: You are Grok 3, built by xAI. The current date is May 09, 2025.

The user has requested a Markdown file documenting the commands for a Discord ticket bot, specifically `/add :user`, `/remove :user`, `/close`, `/setup`, and `/new :user`. They clarified that **all commands are for moderators only**. The previous response provided a Markdown file but was cut off before completion. The task is to provide a complete Markdown file with the same artifact ID as the previous response, updating the permissions to reflect that all commands are moderator-only and ensuring the content is comprehensive, clear, and properly formatted.

Below is the complete Markdown file, wrapped in the required `<xaiArtifact>` tag, using the same `artifact_id` as the previous response. The file includes all requested commands, specifies moderator-only access, and completes the "Notes" section that was truncated. The structure remains consistent with the previous response for continuity, with updates to permissions and a fully fleshed-out "Notes" section.

<xaiArtifact artifact_id="568e5fbd-a968-47a0-9b44-91b5cbe93aed" artifact_version_id="7545e917-2694-4b88-a9f7-2d44082aa3da" title="Discord Ticket Bot Commands" contentType="text/markdown">
# Discord Ticket Bot Commands

This document outlines the commands available for the Discord Ticket Bot, designed to manage support tickets within a Discord server. All commands are slash commands (prefixed with `/`) and are restricted to moderators (users with `Manage Channels` permission or a designated moderator role).

## Table of Contents
- [Command List](#command-list)
  - [/add :user](#add-user)
  - [/remove :user](#remove-user)
  - [/close](#close)
  - [/setup](#setup)
  - [/new :user](#new-user)
- [Permissions Overview](#permissions-overview)
- [Notes](#notes)

## Command List

### /add :user
**Description**: Adds a specified user to the current ticket channel, granting them access to view and participate in the ticket.

**Usage**: `/add :user`

**Parameters**:
- `:user` (required): The user to add to the ticket (mention or ID).

**Example**:
```
/add @JohnDoe
```
- Adds `@JohnDoe` to the ticket, allowing them to see and send messages in the ticket channel.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- The user must be a member of the server.
- Only works in an active ticket channel.
- The bot must have permission to modify channel permissions.

---

### /remove :user
**Description**: Removes a specified user from the current ticket channel, revoking their access to the ticket.

**Usage**: `/remove :user`

**Parameters**:
- `:user` (required): The user to remove from the ticket (mention or ID).

**Example**:
```
/remove @JohnDoe
```
- Removes `@JohnDoe` from the ticket, preventing them from viewing or sending messages in the ticket channel.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- The user must already be added to the ticket.
- Cannot remove the ticket creator or the bot itself.
- The bot must have permission to modify channel permissions.

---

### /close
**Description**: Closes the current ticket, locking the channel and optionally archiving or deleting it based on bot configuration.

**Usage**: `/close`

**Parameters**: None

**Example**:
```
/close
```
- Closes the ticket channel, preventing further messages and marking it as resolved.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- May generate a ticket transcript (if enabled) and send it to a configured log channel.
- Closed tickets may be archived (made read-only) or deleted, depending on bot settings.
- The bot requires `Manage Channels` and `Send Messages` permissions in the log channel (if applicable).

---

### /setup
**Description**: Configures the ticket system for the server, creating a ticket creation channel or panel where users can open tickets.

**Usage**: `/setup`

**Parameters**: None

**Example**:
```
/setup
```
- Creates a ticket creation channel (e.g., `#create-ticket`) with a message or button for users to open tickets.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role. (Note: In some cases, `Administrator` permission may be required for initial setup, depending on bot configuration.)

**Notes**:
- May prompt for additional configuration (e.g., category for ticket channels, support roles) via interactive messages.
- Only one ticket system can be set up per server unless otherwise configured.
- The bot requires `Manage Channels`, `Send Messages`, and `Embed Links` permissions to create the panel.
- Ensure the bot has access to the target category for ticket channels.

---

### /new :user
**Description**: Creates a new ticket for the specified user, opening a private channel for support or discussion.

**Usage**: `/new :user`

**Parameters**:
- `:user` (required): The user for whom the ticket is created (mention or ID).

**Example**:
```
/new @JohnDoe
```
- Creates a new ticket channel accessible to `@JohnDoe`, the moderator issuing the command, and other moderators.

**Permissions**:
- Restricted to moderators with `Manage Channels` permission or a designated moderator role.

**Notes**:
- The ticket channel is typically created under a configured category.
- The bot may notify moderators (e.g., via mentions or a log channel).
- Regular users can create their own tickets via the `/setup`-generated panel, but this command is for moderators to initiate tickets on behalf of users.
- The bot requires `Manage Channels` and `Manage Permissions` to create and configure the ticket channel.

---

## Permissions Overview
- **Moderators**:
  - All commands (`/add`, `/remove`, `/close`, `/setup`, `/new`) are restricted to users with `Manage Channels` permission or a designated moderator role.
  - In some cases, `/setup` may require `Administrator` permission, depending on the bot’s configuration.
- **Regular Users**:
  - Cannot use any of the listed commands.
  - Can create tickets via the ticket panel or message set up with `/setup` (e.g., by clicking a button or reacting to a message).
- **Bot**:
  - Requires `Manage Channels`, `Send Messages`, `Embed Links`, and `Manage Permissions` for most operations.
  - Additional permissions like `View Channels` and `Read Message History` may be needed for full functionality.

Ensure roles and permissions are configured correctly in the Discord server to restrict commands to moderators and allow the bot to function properly.

---

## Notes
- **Bot Configuration**: Features like ticket transcripts, log channels, or archiving behavior depend on the bot’s configuration. Check the bot’s dashboard, configuration file, or setup guide for customization options.
- **Rate Limits**: Discord’s API imposes rate limits. Avoid rapid command usage to prevent temporary restrictions on the bot.
- **Error Handling**: If a command fails, verify that:
  - The user has the required moderator permissions.
  - The bot has necessary permissions (`Manage Channels`, `Manage Permissions`, etc.).
  - The command is used in the correct context (e.g., `/add` or `/remove` in a ticket channel).
- **Ticket Creation for Users**: Regular users can open tickets via the panel or message created by `/setup` (e.g., a button or reaction). Moderators use `/new` to create tickets on behalf of users.
- **Support**: For issues with the bot, consult the bot’s official documentation, repository, or support server (if available). If you’re developing the bot, ensure proper error logging for debugging.
- **Security**: Restrict moderator roles to trusted users to prevent misuse of commands like `/setup` or `/new`.

This documentation assumes a typical ticket bot setup. Adjust configurations as needed for your server’s requirements.