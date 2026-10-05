package stack

import "testing"

func TestStackInt(t *testing.T) {
	var s Stack[int]
	if _, ok := s.Pop(); ok {
		t.Fatal("Pop pada stack kosong harus ok=false")
	}

	s.Push(1)
	s.Push(2)
	s.Push(3)

	if v, _ := s.Peek(); v != 3 {
		t.Errorf("Peek = %d; diharapkan 3", v)
	}
	if s.Len() != 3 {
		t.Errorf("Len = %d; diharapkan 3", s.Len())
	}

	urutan := []int{3, 2, 1}
	for _, harapan := range urutan {
		v, ok := s.Pop()
		if !ok || v != harapan {
			t.Errorf("Pop = %d, %v; diharapkan %d, true", v, ok, harapan)
		}
	}
}

func TestStackString(t *testing.T) {
	s := &Stack[string]{}
	s.Push("a")
	s.Push("b")
	if v, _ := s.Pop(); v != "b" {
		t.Errorf("Pop = %q; diharapkan %q", v, "b")
	}
}
