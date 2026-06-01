export interface ConfirmationDialogConfig {
    header: string;
    body: string[];
    route: string;
    successMessage: string;
    method?: 'post' | 'delete';
}

