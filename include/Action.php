<?php
namespace DB;

enum Action
{
    case Delete;
    case Insert;
    case Update;
}