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
    game_users: Array<GameUser>;
}

export interface GameUser {
    id: number;
    name: string;
    in_game: boolean;
}

export interface PageProps {
    [key: string]: unknown;
    game: Game;
    user: User;
}
