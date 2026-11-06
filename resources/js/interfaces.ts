//models
export interface User {
    id: number;
    name: string;
}

export interface Game {
    id: number;
    name: string;
    private: boolean;
    code: string;
    started: boolean;
    hands: number;
    gameUsers: Array<GameUser>;
}

export interface GameUser {
    id: number;
    user_id: number;
    name: string;
    in_game: boolean;
    start_balance: number;
}

export interface PageProps {
    [key: string]: unknown;
    game: Game;
    user: User;
    gameUser?: GameUser;
    seats: R<string, string | null>;
}

// events
export interface Sit {
    message: string;
}

export interface Stand {
    message: string;
}
