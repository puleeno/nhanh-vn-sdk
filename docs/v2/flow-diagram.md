# Nhanh.vn SDK v2.0 - Flow Diagram

## Khởi tạo và OAuth Flow

```mermaid
flowchart TD
    A[Khởi tạo SDK] --> B[ClientConfig]
    B --> C[appId, businessId, accessToken]
    C --> D[NhanhVnClient::getInstance]
    D --> E[Singleton Instance]
    E --> F[Initialize Modules]
    F --> G[CacheService, ProductManager, ProductRepository, ProductService]

    H[OAuth Flow] --> I[User Authorization]
    I --> J[Redirect to nhanh.vn/oauth]
    J --> K[User Login & Authorize]
    K --> L[Return with access_code]
    L --> M[Callback URL]
    M --> N[exchangeAccessCode]
    N --> O[POST /api/oauth/access_token]
    O --> P[Receive access_token]
    P --> Q[Store access_token]

    E --> R[Configured Client]
    Q --> R
```

## API Request Flow

```mermaid
sequenceDiagram
    participant App as Application
    participant Client as NhanhVnClient
    participant Module as ProductModule
    participant Manager as ProductManager
    participant Service as ProductService
    participant Repo as ProductRepository
    participant Cache as CacheService
    participant API as Nhanh.vn API

    App->>Client: products()->search('iPhone')
    Client->>Module: search('iPhone')
    Module->>Manager: search('iPhone')
    Manager->>Cache: get('search_iPhone')

    alt Cache Hit
        Cache-->>Manager: Return cached data
        Manager-->>Module: Return ProductCollection
        Module-->>Client: Return ProductCollection
        Client-->>App: Return ProductCollection
    else Cache Miss
        Cache-->>Manager: Cache miss
        Manager->>Service: search('iPhone')
        Service->>Repo: search('iPhone')
        Repo->>API: POST /api/product/search
        API-->>Repo: API Response
        Repo->>Service: Create Product entities
        Service->>Manager: Return ProductCollection
        Manager->>Cache: set('search_iPhone', data, 86400)
        Manager-->>Module: Return ProductCollection
        Module-->>Client: Return ProductCollection
        Client-->>App: Return ProductCollection
    end
```

## Error Handling Flow

```mermaid
flowchart TD
    A[API Request] --> B{Request Success?}
    B -->|Yes| C[Return Data]
    B -->|No| D{HTTP Status Code}

    D -->|400| E[InvalidDataException]
    D -->|403| F[UnauthorizedException]
    D -->|429| G[RateLimitException]
    D -->|500+| H[ApiException]

    E --> I[Handle Invalid Data]
    F --> J[Handle Unauthorized]
    G --> K[Handle Rate Limit]
    H --> L[Handle API Error]

    K --> M{Retry Count < Max?}
    M -->|Yes| N[Wait lockedSeconds]
    N --> O[Retry Request]
    O --> A
    M -->|No| P[Throw RateLimitException]

    I --> Q[Log Error]
    J --> Q
    L --> Q
    P --> Q
```

## Cache Management Flow

```mermaid
flowchart TD
    A[Request Data] --> B{Check Cache}
    B -->|Available| C[Return Cached Data]
    B -->|Expired| D[Clear Expired Cache]
    B -->|Not Available| E[Call API]

    D --> E
    E --> F[API Response]
    F --> G{Response Valid?}
    G -->|Yes| H[Create Entities]
    G -->|No| I[Throw Exception]

    H --> J[Store in Cache]
    J --> K[Return Data]

    C --> L[Return Data]
    K --> L

    M[Cache Operations] --> N[getCacheStatus]
    M --> O[clearCache]
    M --> P[isCacheAvailable]

    N --> Q[Return Cache Info]
    O --> R[Clear All Cache]
    P --> S[Return Cache State]
```

## Module Architecture

```mermaid
graph TB
    subgraph "NhanhVnClient (Singleton)"
        A[Client Instance]
        B[Configuration]
        C[Modules]
    end

    subgraph "Product Module"
        D[ProductModule]
        E[ProductManager]
        F[ProductService]
        G[ProductRepository]
    end

    subgraph "Cache Layer"
        H[CacheService]
        I[In-Memory Cache]
        J[TTL Management]
    end

    subgraph "Entity Layer"
        K[Product Entities]
        L[ProductCollection]
        M[Category Entities]
    end

    A --> D
    D --> E
    E --> F
    E --> G
    F --> H
    G --> H
    G --> K
    F --> L
    E --> M

    H --> I
    H --> J
```

## API Endpoints

- **Product Search**: `/api/product/search`
- **Product Detail**: `/api/product/detail`
- **Product Add**: `/api/product/add`
- **Product External Images**: `/api/product/externalimage`
- **Product Categories**: `/api/product/category`
- **Product Brands**: `/api/product/brand`
- **Product Types**: `/api/product/type`
- **Product Suppliers**: `/api/product/supplier`
- **Product Depots**: `/api/product/depot`

## Data Flow

```mermaid
flowchart LR
    A[API Response] --> B[Raw Data]
    B --> C[ProductRepository]
    C --> D[Data Validation]
    D --> E[Entity Creation]
    E --> F[Product Objects]
    F --> G[ProductCollection]
    G --> H[Return to Client]

    I[Cache Layer] --> J[Memory Storage]
    J --> K[TTL Check]
    K --> L[Expiration]
    L --> M[Cleanup]

    N[Error Response] --> O[Exception Mapping]
    O --> P[Custom Exceptions]
    P --> Q[Error Handling]
```
