<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Home extends CI_Controller
{
    private $userData;

    public function __construct()
    {
        parent::__construct();

        $this->userData = $this->session->userdata();
    }

    public function index($categoryId = 0)
    {
        $viewData = [];
        $search = $this->input->get('search');
        $start = (int)$this->input->get('per_page');
        $limit = $this->config->item('per_page');
        $where = [];

        if ($search) {
            $where['title LIKE'] = '%' . $search . '%';
        }
        if ($categoryId) {
            $where['category_id'] = (int)$categoryId;
        }

        $viewData['items'] = $this->db->where($where)->limit($limit, $start)->get('items')->result();

        $paginationUrl = base_url();
        $paginationUrl .= $categoryId ? 'category/' . $categoryId : '';
        $paginationUrl .= $search ? '?search=' . $search : '';

        $this->pagination->initialize([
            'base_url' => $paginationUrl,
            'total_rows' => $this->db->where($where)->count_all_results('items')
        ]);
        $viewData['pagination'] = $this->pagination->create_links();


        $this->render('home', $viewData);
    }

    public function add_cart($itemId)
    {
        if (!isset($this->userData['logged'])) {
            $this->addAlert('warning', 'You must login firstly.');
            redirect(base_url('login'));
        } else {
            $item = $this->db->where('id', $itemId)->get('items')->row();
            if (!is_object($item)) {
                show_404();
            }
            $this->userData['cart'][] = $itemId;
            $this->session->set_userdata('cart', $this->userData['cart']);
            $this->addAlert('success', 'Product successful added to cart');
            redirect(base_url('cart'));
        }
    }

    public function cart()
    {
        if (!isset($this->userData['logged'])) {
            redirect(base_url('login'));
        }

        $delete = $this->input->get('del');
        if ($delete) {
            unset($this->userData['cart'][$delete - 1]);
            $this->session->set_userdata('cart', $this->userData['cart']);
            $this->addAlert('success', 'Successful deleted.');
            redirect(base_url('cart'));
        }

        $data = ['total' => 0];
        foreach ($this->userData['cart'] as $key => $itemId) {
            $item = $this->db->where('id', $itemId)->get('items')->row();
            $data['items'][$key] = $item;
            $data['total'] += $item->price;
        }
        $this->render('cart', $data);
    }

    public function logout()
    {
        $this->session->unset_userdata([
            'logged', 'user_id', 'email', 'first_name', 'last_name'
        ]);
        redirect(base_url());
    }

    public function login()
    {
        if (isset($this->userData['logged'])) {
            redirect(base_url());
        }

        $viewData = [];

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation
            ->set_rules('email', 'Email', 'required|trim|valid_email')
            ->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run()) {
            $user = [
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), 1)
            ];
            $userData = $this->db
                ->select(['id', 'email', 'first_name', 'last_name', 'level'])
                ->where($user)->get('users')->row();
            if (is_object($userData)) {
                $newdata = [
                    'logged' => true,
                    'user_id' => $userData->id,
                    'email' => $userData->email,
                    'level' => $userData->level,
                    'first_name' => $userData->first_name,
                    'last_name' => $userData->last_name
                ];
                $newdata['cart'] = isset($this->userData['cart']) ? $this->userData['cart'] : [];
                $this->session->set_userdata($newdata);
                redirect(base_url());
            } else {
                $viewData['error'] = 'Login or password incorrect.';
            }
        }

        $this->render('login', $viewData);
    }

    public function register()
    {
        if (isset($this->userData['logged'])) {
            redirect(base_url());
        }

        $viewData = [];

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation
            ->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]')
            ->set_rules('first_name', 'First name', 'required|trim|min_length[2]|max_length[50]')
            ->set_rules('last_name', 'Last name', 'required|trim|min_length[2]|max_length[50]')
            ->set_rules('password', 'Password', 'required|trim|min_length[4]|max_length[50]')
            ->set_rules('passconf', 'Password Confirm', 'required|trim|matches[password]');

        if ($this->form_validation->run()) {
            $data = [
                'email' => $this->input->post('email'),
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'password' => password_hash($this->input->post('password'), 1)
            ];
            $insert = $this->db->insert('users', $data);
            if ($insert) {
                $newdata = [
                    'logged' => true,
                    'level' => 0,
                    'user_id' => $this->db->insert_id(),
                    'email' => $data['email'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name']
                ];
                $newdata['cart'] = isset($this->userData['cart']) ? $this->userData['cart'] : [];
                $this->session->set_userdata($newdata);
                $viewData['success'] = true;
            }
        }

        $this->render('register', $viewData);
    }

    private function addAlert($type, $message)
    {
        $alert = ['type' => $type, 'message' => $message];
        $this->session->set_flashdata('alert', $alert);
    }

    private function render($page, $data = [])
    {
        $categories = $this->db->get('categories')->result();
        $headerData = [
            'categories' => $categories,
            'user' => $this->userData,
            'alert' => $this->session->flashdata('alert')
        ];

        $this->load->view('inc/header', $headerData);
        $this->load->view($page, $data);
        $this->load->view('inc/footer');
    }
}
