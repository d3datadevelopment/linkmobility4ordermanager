# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://git.d3data.de/D3Public/Linkmobility4Ordermanager/compare/1.0.0.0...rel_1.x)

## [1.0.0.0](https://git.d3data.de/D3Public/Linkmobility4Ordermanager/releases/tag/1.0.0.0) - 2022-09-01
### Added
- D3 Ordermanager action "send SMS (via LINK mobility)"
  - generate message content from 
    - template file
      - from admin theme
      - from frontend theme
      - from configurable module source
    - CMS item
  - send to (all are optional)
    - order customer
    - free definable recipient numbers