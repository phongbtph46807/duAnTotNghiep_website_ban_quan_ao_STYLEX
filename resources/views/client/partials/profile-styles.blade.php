<style>
    /* Profile Sidebar Styles */
    .profile-sidebar {
        margin-bottom: 20px;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e0e0e0;
        margin: 0 auto;
        display: block;
    }
    .profile-avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6777ef 0%, #764ba2 100%);
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #e0e0e0;
    }

    /* Profile Card Styles */
    .profile-card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    .profile-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #e0e0e0;
        padding: 20px 24px;
    }
    .profile-card-body {
        padding: 30px;
    }

    /* Button Styles */
    .btn-primary-custom {
        background: #6777ef;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-primary-custom:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(103, 119, 239, 0.3);
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    /* Settings Menu Styles */
    .settings-menu-item-sidebar:hover {
        background: #f0f4ff !important;
        color: #6777ef !important;
    }
    .settings-menu-item-sidebar.active {
        background: #f0f4ff !important;
        color: #6777ef !important;
        font-weight: 600;
    }

    /* Form Styles */
    .form-control, .form-select {
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6777ef;
        box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
    }
    .form-check-input:checked {
        background-color: #6777ef;
        border-color: #6777ef;
    }

    /* Address Card Styles */
    .address-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s;
        background: #fff;
    }
    .address-card:hover {
        border-color: #6777ef;
        box-shadow: 0 4px 12px rgba(103, 119, 239, 0.1);
    }
    .address-card.default {
        border-color: #6777ef;
        background: #f0f4ff;
    }
    .address-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .address-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-default {
        background: #6777ef;
        color: white;
    }
    .badge-type {
        background: #f0f0f0;
        color: #666;
        margin-left: 8px;
    }
    .address-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-action {
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-edit {
        background: #6777ef;
        color: white;
    }
    .btn-edit:hover {
        background: #5568d3;
    }
    .btn-delete {
        background: #ff4d4f;
        color: white;
    }
    .btn-delete:hover {
        background: #d9363e;
    }
    .btn-set-default {
        background: #52c41a;
        color: white;
    }
    .btn-set-default:hover {
        background: #389e0d;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    /* Avatar Upload Section */
    .avatar-upload-section {
        text-align: center;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 30px;
    }
    .avatar-preview-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #6777ef;
        cursor: pointer;
        transition: all 0.3s;
        margin: 0 auto 15px;
        display: block;
    }
    .avatar-preview-large:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(103, 119, 239, 0.3);
    }
    .avatar-placeholder-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6777ef 0%, #764ba2 100%);
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #6777ef;
        cursor: pointer;
        transition: all 0.3s;
    }
    .avatar-placeholder-large:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(103, 119, 239, 0.3);
    }

    /* Form Section */
    .form-section {
        margin-bottom: 30px;
    }
    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-card-body {
            padding: 20px;
        }
        .avatar-upload-section {
            padding: 20px;
        }
        .address-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .address-actions {
            width: 100%;
        }
    }
</style>

