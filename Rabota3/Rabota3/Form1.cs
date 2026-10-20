using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Rabota3
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private void BtnDobavit_Click(object sender, EventArgs e)
        {
            Form2 Okno = new Form2();
            Okno.Text = "Добавление блюда";
            if (Okno.ShowDialog() == DialogResult.OK)
            {
                Blyudo Item = new Blyudo();
                Item.Nazvanie = Okno.Nazv_TB.Text.Trim();
                Item.Ves = Convert.ToDouble(Okno.Ves_TB.Text);
                Item.Sebestoimost = Convert.ToInt32(Okno.Seb_TB.Text);
                Item.Cena = Convert.ToInt32(Okno.Cena_TB.Text);
                listBox1.Items.Add(Item);
            }
        }

        private void BtnIzmenit_Click(object sender, EventArgs e)
        {
            if (listBox1.SelectedIndex != -1)
            {
                Blyudo Item = listBox1.SelectedItem as Blyudo;
                Form2 Okno = new Form2();
                Okno.Text = "Изменение блюда";
                Okno.Nazv_TB.Text = Item.Nazvanie;
                Okno.Ves_TB.Text = Item.Ves.ToString();
                Okno.Seb_TB.Text = Item.Sebestoimost.ToString();
                Okno.Cena_TB.Text = Item.Cena.ToString();
                if (Okno.ShowDialog() == DialogResult.OK)
                {
                    Item.Nazvanie = Okno.Nazv_TB.Text.Trim();
                    Item.Ves = Convert.ToDouble(Okno.Ves_TB.Text);
                    Item.Sebestoimost = Convert.ToInt32(Okno.Seb_TB.Text);
                    Item.Cena = Convert.ToInt32(Okno.Cena_TB.Text);
                    listBox1.Items[listBox1.SelectedIndex] = Item;
                }
            }
            else
                MessageBox.Show("Не выбран элемент в списке", "Информация");
        }

        private void BtnUdalit_Click(object sender, EventArgs e)
        {
            if (listBox1.SelectedIndex != -1)
                listBox1.Items.RemoveAt(listBox1.SelectedIndex);
            else
                MessageBox.Show("Не выбран элемент в списке", "Информация");
        }
    }
}
