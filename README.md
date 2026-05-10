# Social Network PHP Web Application

This is a simple Social Network web application built with PHP, MySQL, Nginx, and Linux.

## Tech Stack

- PHP
- MySQL / MariaDB
- Nginx
- Linux

## Main Features

- Admin can create new user accounts.
- Users can sign in and sign out.
- The application uses PHP Session to keep the login state.
- Users can view their own profile.
- Users can edit their profile content in the Setting page.
- Users can view other users in the system.
- Users can send friend requests to other users.
- Friend requests have a `pending` status before being accepted or rejected.
- Users can accept or reject friend requests.
- Accepted users will appear in the friend list.

## Extended Feature

In addition to the original assignment requirements, I added a friendship request system.

The friendship system includes:

- Add Friend
- Pending Friend Request
- Accept Friend Request
- Reject Friend Request
- Friend List

The friend request data is stored in the `friend_request` table.

The `status` column is used to control the request state:

```text
pending  = the request has been sent but not answered yet
accepted = the request has been accepted and the users are friends
rejected = the request has been rejected
